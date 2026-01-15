<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * 5問の回答から複数タイプのお酒にスコア加算し、
 * { primary, candidates, mood, top5, scores_map } を返すサービス。
 *
 * ■ 質問セット
 * - 固定2問: q1, q2
 * - カテゴリA/B/Cから各1問ランダム
 *   => 合計5問を createSession() で返す。
 *
 * ■ mood（気分タグ）
 * - q1 の回答で決定
 *   a = lively   (わいわい飲みたい)
 *   b = chill    (少人数でしっぽり)
 *   c = silent   (ひとりで静かに)
 *   d = light    (サクッと飲みたい)
 *   e = strong   (がっつり飲みたい)
 *
 * ■ スコア加算
 * - config('diagnose.weights') の
 *   "<質問キー>:<選択肢キー>" => [type => point, ...]
 *   を使って各タイプに加点
 * - q2 だけ multiplier（例：1.5）を掛けて強めに反映
 *
 * ■ 候補（candidates）
 * - 最大スコアとの差が candidate_width 以内のものを候補とし、
 *   スコア降順で並べる（既存ロジックを維持）
 *
 * ■ チャート（top5）
 * - 候補抽出とは別枠で、全タイプのスコアから上位5種類を返す
 *   => “提案は1位(primary)だけ、チャートは上位5” を実現する
 */
class DiagnoseService
{
    /**
     * 設定配列（config('diagnose')）
     *
     * @var array<string, mixed>
     */
    private array $conf;

    /**
     * q1 の回答(a〜e) -> moodタグ 変換テーブル
     *
     * @var array<string, string>
     */
    private array $moodMap = [
        'a' => 'lively',
        'b' => 'chill',
        'c' => 'silent',
        'd' => 'light',
        'e' => 'strong',
    ];

    public function __construct()
    {
        $fromConfig = config('diagnose');
        $this->conf = is_array($fromConfig) ? $fromConfig : $this->defaultConf();
    }

    /**
     * 質問セッションを作る（固定2 + A/B/C 各1問。足りなければ5問になるまで補充）
     *
     * - fixed_questions: config('diagnose.fixed_questions')
     *   例）[ [..q1..], [..q2..] ]
     *
     * - categories: config('diagnose.categories')
     *   例）[
     *      'A' => [ [..A1..], [..A2..], ... ],
     *      'B' => [ ... ],
     *      'C' => [ ... ],
     *   ]
     *
     * A/B/C は複数あればランダムで1件ずつ取得。
     * どこか欠けていても、可能な限り5問になるように他の質問から補う。
     *
     * @param  int|null  $seed  同じseedなら同じ問題セットになる
     * @return array{seed:int|null, questions: array<int, array<string, mixed>>}
     */
    public function createSession(?int $seed = null): array
    {
        $fixed = $this->conf['fixed_questions'] ?? [];
        $cats  = $this->conf['categories']      ?? [];

        // seed 未指定なら自前で発番して固定
        if ($seed === null) {
            try {
                $seed = random_int(1, PHP_INT_MAX);
            } catch (\Exception $e) {
                // random_int が使えない環境向けのフォールバック
                $seed = mt_rand();
            }
        }
        mt_srand($seed);

        $questions = [];

        // ----------------------------
        // 固定質問（q1, q2）を先に詰める
        // ----------------------------
        foreach ($fixed as $q) {
            if (isset($q['id'], $q['text'], $q['choices'])) {
                $questions[] = $q;
            }
        }

        // ----------------------------
        // A / B / C から 1問ずつ選ぶ
        // ----------------------------
        $pickedIds = [];
        foreach ($questions as $q) {
            if (isset($q['id'])) {
                $pickedIds[$q['id']] = true;
            }
        }

        foreach (['A', 'B', 'C'] as $groupKey) {
            if (count($questions) >= 5) {
                break;
            }

            if (!isset($cats[$groupKey]) || !is_array($cats[$groupKey]) || count($cats[$groupKey]) === 0) {
                Log::warning("[Diagnose] category {$groupKey} is empty. Check config/diagnose.php");
                continue;
            }

            $group = $cats[$groupKey];
            $idx   = mt_rand(0, count($group) - 1);
            $q     = $group[$idx];

            if (!isset($q['id'], $q['text'], $q['choices'])) {
                continue;
            }
            if (isset($pickedIds[$q['id']])) {
                continue;
            }

            $questions[]         = $q;
            $pickedIds[$q['id']] = true;
        }

        // ----------------------------
        // まだ5問に満たない場合は、残りを全カテゴリから補充
        // ----------------------------
        if (count($questions) < 5) {
            $pool = [];

            foreach ($cats as $groupKey => $group) {
                if (!is_array($group)) {
                    continue;
                }
                foreach ($group as $q) {
                    if (!isset($q['id'], $q['text'], $q['choices'])) {
                        continue;
                    }
                    if (isset($pickedIds[$q['id']])) {
                        continue;
                    }
                    $pool[] = $q;
                }
            }

            while (count($questions) < 5 && count($pool) > 0) {
                $idx = mt_rand(0, count($pool) - 1);
                $q   = $pool[$idx];

                $questions[]         = $q;
                $pickedIds[$q['id']] = true;

                // 同じものを2回引かないようにプールから削除
                array_splice($pool, $idx, 1);
            }
        }

        // デバッグ用ログ（あとで邪魔なら消してOK）
        Log::info('[Diagnose] createSession', [
            'seed'            => $seed,
            'fixed_count'     => is_array($fixed) ? count($fixed) : 0,
            'categories_keys' => array_keys($cats),
            'question_count'  => count($questions),
            'question_ids'    => array_map(fn ($q) => $q['id'] ?? null, $questions),
        ]);

        return [
            'seed'      => $seed,
            'questions' => array_values($questions),
        ];
    }

    /**
     * 採点処理
     *
     * @param  array<string, string>  $answers
     *   例：['q1' => 'a', 'q2' => 'c', 'A1' => 'b', ...]
     * @param  int|null               $seed   // 将来の拡張用。今はスコアには未使用。
     *
     * @return array{
     *   primary: string|null,
     *   candidates: array<int, array{type:string, score:float, label:string}>,
     *   top5: array<int, array{type:string, score:float, label:string}>,
     *   scores_map: array<string, float>,
     *   mood: string|null
     * }
     */
    public function score(array $answers, ?int $seed = null): array
    {
        // seed は今のところ使っていないが、Controller との引数合わせのため受け取っておく
        if ($seed !== null) {
            Log::info('[Diagnose] score called with seed', ['seed' => $seed]);
        }

        $types = $this->conf['types'] ?? [];
        if (empty($types)) {
            Log::error('[Diagnose] types is empty. Check config/diagnose.php');

            return [
                'primary'    => null,
                'candidates' => [],
                'top5'       => [],
                'scores_map' => [],
                'mood'       => null,
            ];
        }

        $weights    = $this->conf['weights'] ?? [];
        $labels     = $this->conf['labels'] ?? [];
        $multiplier = (float)($this->conf['scoring']['q2_multiplier'] ?? 1.0);
        $width      = (float)($this->conf['scoring']['candidate_width'] ?? 3.0);

        // ---------------------------
        // 全タイプ初期化（type => score）
        // ※ $types は「タイプキーの配列」を想定（例：['nihonshu', 'wine', ...]）
        // ---------------------------
        $scores = [];
        foreach ($types as $t) {
            $scores[$t] = 0.0;
        }

        // ---------------------------
        // mood: q1 の回答から取得
        // ---------------------------
        $mood = null;
        if (isset($answers['q1'])) {
            $answerKey = $answers['q1']; // a〜e 想定
            $mood      = $this->moodMap[$answerKey] ?? null;
        }

        // ---------------------------
        // スコア加点ループ
        // ---------------------------
        foreach ($answers as $qKey => $choiceKey) {
            // 例： "q2" + "a" -> "q2:a"
            $mapKey = "{$qKey}:{$choiceKey}";
            if (!isset($weights[$mapKey]) || !is_array($weights[$mapKey])) {
                continue;
            }

            // q2だけ倍率をかける
            $factor = ($qKey === 'q2') ? $multiplier : 1.0;

            // この回答で加点されるタイプのリスト
            $scoredTypes = [];
            foreach ($weights[$mapKey] as $type => $point) {
                if (!array_key_exists($type, $scores)) {
                    continue;
                }
                $scores[$type] += (float) $point * $factor;
                $scoredTypes[] = $type;
            }

            // 5種類未満の場合は、残りのタイプに小さな点数を追加
            if (count($scoredTypes) < 5) {
                $remainingTypes = array_diff($types, $scoredTypes);
                $fillCount = 5 - count($scoredTypes);
                $fillScore = 0.5; // 小さな点数

                // 残りのタイプからランダムに選ぶ（ただし、優先順位を考慮）
                $priorityTypes = ['sake_dry', 'sake_sweet', 'shochu_kome', 'wine_white'];
                $priorityRemaining = array_intersect($priorityTypes, $remainingTypes);
                $otherRemaining = array_diff($remainingTypes, $priorityTypes);
                
                // 優先タイプから先に選ぶ
                $selectedTypes = array_slice($priorityRemaining, 0, $fillCount);
                $remainingFillCount = $fillCount - count($selectedTypes);
                if ($remainingFillCount > 0) {
                    $selectedTypes = array_merge($selectedTypes, array_slice($otherRemaining, 0, $remainingFillCount));
                }

                foreach ($selectedTypes as $type) {
                    $scores[$type] += $fillScore * $factor;
                }
            }
        }

        // ---------------------------
        // 全タイプをスコア降順に並べた “ランキング用Map” を作る
        // ---------------------------
        $sorted = $scores;   // type => score
        arsort($sorted);     // 値（score）で降順、キー保持

        $max = 0.0;
        if (!empty($sorted)) {
            $max = (float) reset($sorted); // 先頭の値（最大スコア）
        }

        // ---------------------------
        // 候補抽出（既存仕様を維持）
        // ---------------------------
        $candidates = [];
        foreach ($scores as $type => $s) {
            // 最大スコアとの差が width 以内のみ候補にする
            if ($max - $s <= $width) {
                $candidates[] = [
                    'type'  => $type,
                    'score' => round((float) $s, 2),
                    'label' => $labels[$type] ?? $type,
                ];
            }
        }

        // スコア降順でソート（候補表示の順序を保証）
        usort($candidates, fn ($a, $b) => $b['score'] <=> $a['score']);

        $primary = $candidates[0]['type'] ?? (array_key_first($sorted) ?: null);

        // ---------------------------
        // チャート用：上位5種類（候補幅とは別に、純粋なランキング上位）
        // ---------------------------
        $top5Map = array_slice($sorted, 0, 5, true); // type => score（キー保持）
        $top5    = [];
        foreach ($top5Map as $type => $s) {
            $top5[] = [
                'type'  => $type,
                'score' => round((float) $s, 2),
                'label' => $labels[$type] ?? $type,
            ];
        }

        // top5が5種類未満の場合は、スコア0のタイプを優先順位で追加
        if (count($top5) < 5) {
            $top5Types = array_column($top5, 'type');
            $remainingTypes = array_diff($types, $top5Types);
            
            // 優先順位: 日本酒（sake_dry, sake_sweet）、米焼酎（shochu_kome）、白ワイン（wine_white）
            $priorityOrder = ['sake_dry', 'sake_sweet', 'shochu_kome', 'wine_white'];
            
            // 優先タイプから順に追加
            $priorityRemaining = array_intersect($priorityOrder, $remainingTypes);
            $otherRemaining = array_diff($remainingTypes, $priorityOrder);
            
            $fillTypes = array_merge($priorityRemaining, $otherRemaining);
            $fillCount = 5 - count($top5);
            
            foreach (array_slice($fillTypes, 0, $fillCount) as $type) {
                $top5[] = [
                    'type'  => $type,
                    'score' => 0.0,
                    'label' => $labels[$type] ?? $type,
                ];
            }
        }

        return [
            'primary'    => $primary,                 // 1位（提案に使う）
            'candidates' => $candidates,              // 既存仕様（候補表示に使う）
            'top5'       => $top5,                    // チャート用（上位5）
            'scores_map' => array_map(
                fn ($v) => round((float) $v, 2),
                $sorted
            ),                                        // 全タイプ（将来の拡張・デバッグ用）
            'mood'       => $mood,
        ];
    }

    /**
     * 設定が無いときの安全デフォルト（最小構成）
     * ※ 質問セットまでは持たず、types/labels だけ。
     *
     * @return array<string, mixed>
     */
    private function defaultConf(): array
    {
        return [
            'types'           => ['craft_beer'],
            'weights'         => [],
            'fixed_questions' => [],
            'categories'      => [],
            'scoring'         => [
                'q2_multiplier'   => 1.0,
                'candidate_width' => 3.0,
            ],
            'labels'          => [
                'craft_beer' => 'クラフトビール',
            ],
        ];
    }
}
