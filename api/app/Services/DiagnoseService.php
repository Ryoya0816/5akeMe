<?php

namespace App\Services;

/**
 * 診断の中核ロジック。
 * - 出題：固定2（q1,q2）＋カテゴリA/B/Cから各1問（seedで再現性）
 * - 集計：各選択肢のタイプ別スコアを加算、q2のみ×1.5
 * - 候補：最大スコアから「3点幅」以内を候補に残し、同点はfallback順で決定
 * - mood：q1の回答から 1=わいわい / 2=しっとり を返す
 *
 * 設定は config('diagnose') を優先し、無ければ $this->defaultConf を使用。
 */
class DiagnoseService
{
    private array $conf;

    public function __construct()
    {
        $fromConfig = config('diagnose');
        $this->conf = is_array($fromConfig) ? $fromConfig : $this->defaultConf();
    }

    /** 出題（固定2＋A/B/C各1）。seedがあれば再現可能なランダム */
    public function createSession(?int $seed = null): array
    {
        if ($seed === null) {
            $seed = now()->timestamp;
        }
        mt_srand($seed);

        $qs   = collect($this->getQuestions());
        $byCat = $qs->groupBy('category');

        // 固定
        $fixed = $byCat['fixed']->pluck('id')->all(); // ["q1","q2"]

        // 各カテゴリから1問ずつランダム
        $pick = function (string $cat) use ($byCat): string {
            $arr = $byCat[$cat]->pluck('id')->values()->all();
            return $arr[mt_rand(0, count($arr) - 1)];
        };
        $A = $pick('A');
        $B = $pick('B');
        $C = $pick('C');

        return [
            'seed'         => $seed,
            'question_ids' => array_merge($fixed, [$A, $B, $C]),
        ];
    }

    /** 回答を集計し、type・候補・mood を返す */
    public function score(array $answers): array
    {
        $qs      = collect($this->getQuestions())->keyBy('id');
        $weights = $this->conf['weights'] ?? [];
        $scores  = []; // type_code => float

        foreach ($answers as $a) {
            $qid   = $a['id']    ?? null;
            $label = $a['label'] ?? null;
            if (!$qid || !$label) continue;

            $q   = $qs[$qid] ?? null;
            if (!$q || empty($q['options'])) continue;

            $opt = collect($q['options'])->firstWhere('label', $label);
            if (!$opt) continue;

            $w = $weights[$qid] ?? 1.0; // q2は1.5 など
            foreach (($opt['scores'] ?? []) as $type => $pt) {
                $scores[$type] = ($scores[$type] ?? 0) + $pt * $w;
            }
        }

        $mood       = $this->resolveMood($answers);
        $candidates = $this->candidates($scores);
        $primary    = $candidates[0] ?? null;

        return [
            'scores'     => $scores,      // 全タイプの素点（小数含む）
            'candidates' => $candidates,  // 3点幅内の順序付き候補
            'primary'    => $primary,     // 第1候補
            'mood'       => $mood,        // 1=わいわい / 2=しっとり
        ];
    }

    /** 設問一覧 */
    public function getQuestions(): array
    {
        return $this->conf['questions'] ?? [];
    }

    /** q1の回答からムードを決定 */
    private function resolveMood(array $answers): int
    {
        $map = $this->conf['mood_map'] ?? [];
        // q1 を探す
        foreach ($answers as $a) {
            if (($a['id'] ?? null) === 'q1') {
                $label = $a['label'] ?? null;
                return $map[$label] ?? 1;
            }
        }
        return 1;
    }

    /** 3点幅 & 同点fallbackで候補を並べる */
    private function candidates(array $scores): array
    {
        if (empty($scores)) return [];
        $max    = max($scores);
        $band   = $this->conf['top_band_delta'] ?? 3;
        $fb     = $this->conf['fallback_order'] ?? [];

        // 3点幅内だけ対象
        $inBand = array_filter($scores, fn($v) => $max - $v <= $band);
        $keys   = array_keys($inBand);

        // 降順。スコア同点は fallback_order の早い方を優先
        usort($keys, function ($a, $b) use ($inBand, $fb) {
            if ($inBand[$a] == $inBand[$b]) {
                $ia = array_search($a, $fb);
                $ib = array_search($b, $fb);
                // 見つからないときは末尾扱い
                $ia = $ia === false ? PHP_INT_MAX : $ia;
                $ib = $ib === false ? PHP_INT_MAX : $ib;
                return $ia <=> $ib;
            }
            return $inBand[$b] <=> $inBand[$a];
        });

        return array_values($keys);
    }

    /** デフォルト設定（config が無い場合のフォールバック） */
    private function defaultConf(): array
    {
        return [
            // 同点時の優先順位
            'fallback_order' => ['SA-K','WW','RW','SH-I','SP','CB','SA-A','SH-M','SH-K','CT'],
            // 重み：q2のみ1.5倍
            'weights' => ['q1' => 1.0, 'q2' => 1.5],
            // 3点幅
            'top_band_delta' => 3,
            // q1 → mood
            'mood_map' => [
                'スッキリ'           => 1,
                '盛り上がりたい'     => 1,
                'じっくり語りたい'   => 2,
                'しっとり落ち着きたい' => 2,
                '静かに味わいたい'   => 2,
            ],
            // 設問（固定2＋A/B/C 各5問）。必要十分の最小セットを内包。
            'questions' => [
                // ==== fixed ====
                [
                    'id' => 'q1', 'category' => 'fixed', 'text' => '今の気分／テンションは？',
                    'options' => [
                        ['label'=>'スッキリ',             'scores'=>['SA-K'=>3,'WW'=>2,'SP'=>1,'CB'=>1]],
                        ['label'=>'じっくり語りたい',     'scores'=>['RW'=>3,'SH-I'=>1,'SA-A'=>2,'SH-K'=>1]],
                        ['label'=>'盛り上がりたい',       'scores'=>['SP'=>3,'CB'=>3,'WW'=>1,'CT'=>1]],
                        ['label'=>'しっとり落ち着きたい', 'scores'=>['SA-A'=>3,'SH-K'=>2,'RW'=>1]],
                        ['label'=>'静かに味わいたい',     'scores'=>['SH-K'=>3,'SA-A'=>2,'RW'=>1]],
                    ]
                ],
                [
                    'id' => 'q2', 'category' => 'fixed', 'text' => 'お酒に求めるものは？',
                    'options' => [
                        ['label'=>'香り',           'scores'=>['WW'=>3,'SP'=>3,'CT'=>1,'SA-A'=>2]],
                        ['label'=>'コク',           'scores'=>['RW'=>3,'SH-I'=>1,'SH-K'=>2,'SA-A'=>1]],
                        ['label'=>'スッキリ感',     'scores'=>['SA-K'=>3,'SH-M'=>3,'WW'=>1,'CB'=>1]],
                        ['label'=>'温めてほっと',   'scores'=>['SH-K'=>3,'SA-A'=>3,'SH-I'=>1,'SA-K'=>2]],
                        ['label'=>'飲みやすさ',     'scores'=>['SA-A'=>3,'WW'=>2,'CT'=>2,'SP'=>1]],
                    ]
                ],

                // ==== A ====
                [
                    'id'=>'qA1','category'=>'A','text'=>'今回の主役は？',
                    'options'=>[
                        ['label'=>'魚',   'scores'=>['SA-K'=>3,'WW'=>3,'SP'=>1,'SA-A'=>1]],
                        ['label'=>'肉',   'scores'=>['RW'=>3,'SH-I'=>1,'CB'=>2,'SH-M'=>1]],
                        ['label'=>'揚げ物','scores'=>['CB'=>3,'SP'=>3,'SA-K'=>1]],
                        ['label'=>'濃い味','scores'=>['RW'=>3,'SH-I'=>1,'SA-A'=>1,'SH-K'=>2]],
                        ['label'=>'野菜', 'scores'=>['WW'=>3,'SA-K'=>2,'SP'=>1]],
                    ],
                ],
                [
                    'id'=>'qA2','category'=>'A','text'=>'一緒に飲みたいのは？',
                    'options'=>[
                        ['label'=>'職場仲間','scores'=>['SH-M'=>3,'CB'=>2,'SP'=>1]],
                        ['label'=>'親友',   'scores'=>['CB'=>3,'RW'=>1,'SA-K'=>1]],
                        ['label'=>'恋人',   'scores'=>['WW'=>2,'SP'=>2,'SA-A'=>1]],
                        ['label'=>'家族',   'scores'=>['SA-A'=>2,'SH-K'=>2,'SA-K'=>1]],
                        ['label'=>'一人',   'scores'=>['RW'=>2,'SH-K'=>2,'SA-A'=>1]],
                    ],
                ],
                [
                    'id'=>'qA3','category'=>'A','text'=>'甘党 or 辛党？',
                    'options'=>[
                        ['label'=>'甘党強め','scores'=>['SA-A'=>3,'CT'=>2,'SP'=>1]],
                        ['label'=>'やや甘党','scores'=>['SA-A'=>2,'WW'=>2,'CT'=>1]],
                        ['label'=>'中間',   'scores'=>['WW'=>2,'SA-K'=>2]],
                        ['label'=>'やや辛党','scores'=>['SA-K'=>3,'RW'=>1,'SH-M'=>1]],
                        ['label'=>'辛党強め','scores'=>['SA-K'=>3,'RW'=>2,'SH-M'=>1]],
                    ],
                ],
                [
                    'id'=>'qA4','category'=>'A','text'=>'今のおなか空き量は？',
                    'options'=>[
                        ['label'=>'がっつり','scores'=>['RW'=>2,'SH-I'=>1,'CB'=>2]],
                        ['label'=>'ほどほど','scores'=>['SH-M'=>2,'SA-K'=>1]],
                        ['label'=>'軽く',  'scores'=>['WW'=>2,'SP'=>1]],
                        ['label'=>'つまむ程度','scores'=>['WW'=>2,'SA-A'=>1]],
                        ['label'=>'食べない','scores'=>['SP'=>2,'SA-A'=>1]],
                    ],
                ],
                [
                    'id'=>'qA5','category'=>'A','text'=>'お酒を飲む時に重視する感覚は？',
                    'options'=>[
                        ['label'=>'爽快',       'scores'=>['SA-K'=>3,'CB'=>2,'SP'=>1]],
                        ['label'=>'香り',       'scores'=>['WW'=>3,'SP'=>2,'SA-A'=>1]],
                        ['label'=>'余韻',       'scores'=>['RW'=>3,'SH-I'=>1,'SA-A'=>1]],
                        ['label'=>'食事との一体感','scores'=>['SA-K'=>2,'WW'=>2,'RW'=>1]],
                        ['label'=>'雰囲気',     'scores'=>['SP'=>2,'SA-A'=>1,'RW'=>1]],
                    ],
                ],

                // ==== B ====
                [
                    'id'=>'qB1','category'=>'B','text'=>'休日の過ごし方は？',
                    'options'=>[
                        ['label'=>'アウトドア','scores'=>['CB'=>3,'SA-K'=>2]],
                        ['label'=>'家でまったり','scores'=>['SA-A'=>3,'SH-K'=>2]],
                        ['label'=>'旅行',     'scores'=>['SP'=>3,'WW'=>2]],
                        ['label'=>'街歩き',   'scores'=>['WW'=>3,'RW'=>1]],
                        ['label'=>'趣味没頭', 'scores'=>['RW'=>2,'SA-K'=>1,'SH-K'=>1]],
                    ],
                ],
                [
                    'id'=>'qB2','category'=>'B','text'=>'理想の旅先は？',
                    'options'=>[
                        ['label'=>'温泉','scores'=>['SH-K'=>3,'SA-A'=>2]],
                        ['label'=>'海外','scores'=>['SP'=>3,'RW'=>1]],
                        ['label'=>'自然','scores'=>['SA-K'=>3,'SH-I'=>1,'SA-A'=>1]],
                        ['label'=>'都会','scores'=>['WW'=>3,'CT'=>1]],
                        ['label'=>'食の街','scores'=>['RW'=>2,'WW'=>2,'SA-A'=>1]],
                    ],
                ],
                [
                    'id'=>'qB3','category'=>'B','text'=>'お金をかけるのは？',
                    'options'=>[
                        ['label'=>'食事','scores'=>['RW'=>2,'WW'=>2,'SA-K'=>1]],
                        ['label'=>'お酒','scores'=>['SA-K'=>2,'RW'=>2,'WW'=>1]],
                        ['label'=>'旅行','scores'=>['SP'=>3,'WW'=>1]],
                        ['label'=>'ガジェット','scores'=>['CB'=>2,'WW'=>1]],
                        ['label'=>'ファッション','scores'=>['SP'=>2,'CT'=>2]],
                    ],
                ],
                [
                    'id'=>'qB4','category'=>'B','text'=>'ストレス発散方法は？',
                    'options'=>[
                        ['label'=>'運動','scores'=>['CB'=>3,'SA-K'=>1]],
                        ['label'=>'睡眠','scores'=>['SA-A'=>3,'SH-K'=>1]],
                        ['label'=>'食べ歩き','scores'=>['RW'=>2,'WW'=>1,'SA-K'=>1]],
                        ['label'=>'カラオケ','scores'=>['SP'=>2,'CB'=>2]],
                        ['label'=>'ひとり時間','scores'=>['SH-K'=>2,'RW'=>1,'SA-A'=>1]],
                    ],
                ],
                [
                    'id'=>'qB5','category'=>'B','text'=>'新しいことへの挑戦度は？',
                    'options'=>[
                        ['label'=>'とても高い','scores'=>['CB'=>2,'SP'=>2,'CT'=>1]],
                        ['label'=>'やや高い','scores'=>['SP'=>2,'WW'=>1]],
                        ['label'=>'ふつう','scores'=>['WW'=>1,'SA-K'=>1]],
                        ['label'=>'やや低い','scores'=>['SA-A'=>2,'SH-K'=>1]],
                        ['label'=>'低い','scores'=>['SH-K'=>2,'SA-A'=>1]],
                    ],
                ],

                // ==== C ====
                [
                    'id'=>'qC1','category'=>'C','text'=>'自分にとってのお酒とは？',
                    'options'=>[
                        ['label'=>'ご褒美','scores'=>['SP'=>2,'CT'=>1,'SA-A'=>2]],
                        ['label'=>'潤滑油','scores'=>['SH-M'=>3,'SA-K'=>1,'CB'=>1]],
                        ['label'=>'趣味','scores'=>['RW'=>3,'SA-K'=>1,'SH-I'=>1]],
                        ['label'=>'冒険','scores'=>['CB'=>3,'SP'=>2,'CT'=>1]],
                        ['label'=>'社交','scores'=>['SP'=>2,'WW'=>1,'CB'=>1]],
                    ],
                ],
                [
                    'id'=>'qC2','category'=>'C','text'=>'普段聞く音楽は？',
                    'options'=>[
                        ['label'=>'ロック','scores'=>['CB'=>2,'SH-I'=>1]],
                        ['label'=>'ジャズ','scores'=>['RW'=>2,'SA-A'=>1]],
                        ['label'=>'クラシック','scores'=>['WW'=>3,'SA-K'=>1]],
                        ['label'=>'ポップ','scores'=>['SP'=>2,'CT'=>1]],
                        ['label'=>'EDM','scores'=>['SP'=>2,'CB'=>1]],
                    ],
                ],
                [
                    'id'=>'qC3','category'=>'C','text'=>'お酒を人に例えると？',
                    'options'=>[
                        ['label'=>'兄貴肌','scores'=>['SH-I'=>1,'RW'=>2]],
                        ['label'=>'癒し系','scores'=>['SA-A'=>3,'SH-K'=>1]],
                        ['label'=>'不思議系','scores'=>['WW'=>3,'CT'=>1]],
                        ['label'=>'華やか','scores'=>['SP'=>3,'CT'=>1]],
                        ['label'=>'職人肌','scores'=>['SA-K'=>2,'RW'=>1]],
                    ],
                ],
                [
                    'id'=>'qC4','category'=>'C','text'=>'お酒飲みながらしたいことは？',
                    'options'=>[
                        ['label'=>'語る','scores'=>['RW'=>2,'SA-A'=>1,'SH-K'=>1]],
                        ['label'=>'食べる','scores'=>['SA-K'=>2,'WW'=>2,'RW'=>1]],
                        ['label'=>'音楽','scores'=>['SP'=>1,'WW'=>1,'CT'=>2]],
                        ['label'=>'映画/配信','scores'=>['SA-A'=>2,'RW'=>1]],
                        ['label'=>'ゲーム','scores'=>['CB'=>2,'SP'=>1]],
                    ],
                ],
                [
                    'id'=>'qC5','category'=>'C','text'=>'飲み会で一番盛り上がる話題は？',
                    'options'=>[
                        ['label'=>'仕事','scores'=>['SH-M'=>2,'RW'=>1]],
                        ['label'=>'恋バナ','scores'=>['WW'=>2,'SP'=>1,'CT'=>1]],
                        ['label'=>'グルメ','scores'=>['RW'=>2,'WW'=>1,'SA-K'=>1]],
                        ['label'=>'趣味','scores'=>['CB'=>2,'RW'=>1]],
                        ['label'=>'昔話','scores'=>['SA-A'=>2,'SH-K'=>1]],
                    ],
                ],
            ],
        ];
    }
}
