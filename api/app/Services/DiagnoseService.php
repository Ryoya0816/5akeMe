<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * 5問の回答から10タイプにスコア加算し、{primary,candidates,mood} を返す。
 * - moodは q1 の回答のみで決める（a=1 わいわい / b=2 しっとり）
 * - q2は multiplier を掛けて少し強めに反映
 * - 候補は最大スコアから設定幅以内を残す
 */
class DiagnoseService
{
    private array $conf;

    public function __construct()
    {
        $fromConfig = config('diagnose');
        $this->conf = is_array($fromConfig) ? $fromConfig : $this->defaultConf();
    }

    /** 質問セッションを作る（固定2 + A/B/C各1問、seedは未使用でも可） */
    public function createSession(?int $seed = null): array
    {
        $fixed = $this->conf['fixed_questions'] ?? [];
        $cats  = $this->conf['categories'] ?? [];

        // 今は単一問題しかないのでそのまま採用（教材A/B/Cから各1）
        $pick = [];
        foreach (['A','B','C'] as $g) {
            if (!empty($cats[$g][0])) $pick[] = $cats[$g][0];
        }

        return [
            'questions' => array_merge($fixed, $pick),
        ];
    }

    /** 採点 */
    public function score(array $answers): array
    {
        $types = $this->conf['types'] ?? [];
        if (empty($types)) {
            Log::error('[Diagnose] types is empty. Check config/diagnose.php');
            return ['primary'=>null,'candidates'=>[],'mood'=>null];
        }

        $weights    = $this->conf['weights'] ?? [];
        $labels     = $this->conf['labels'] ?? [];
        $multiplier = (float)($this->conf['scoring']['q2_multiplier'] ?? 1.0);
        $width      = (float)($this->conf['scoring']['candidate_width'] ?? 3.0);

        // 初期化
        $scores = [];
        foreach ($types as $t) $scores[$t] = 0.0;

        // mood: q1で決定（a=1, b=2）
        $mood = null;
        if (isset($answers['q1'])) {
            $mood = ($answers['q1'] === 'a') ? 1 : 2;
        }

        // 加点ループ
        foreach ($answers as $qKey => $choiceKey) {
            $key = "{$qKey}:{$choiceKey}";
            if (!isset($weights[$key])) continue;

            $factor = ($qKey === 'q2') ? $multiplier : 1.0;
            foreach ($weights[$key] as $type => $point) {
                if (!array_key_exists($type, $scores)) continue;
                $scores[$type] += $point * $factor;
            }
        }

        // 上位候補を作成
        $max = max($scores ?: [0]);
        $candidates = [];
        foreach ($scores as $type => $s) {
            if ($max - $s <= $width) {
                $candidates[] = ['type' => $type, 'score' => round($s, 2), 'label' => $labels[$type] ?? $type];
            }
        }

        // 降順ソート
        usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
        $primary = $candidates[0]['type'] ?? null;

        return [
            'primary'    => $primary,
            'candidates' => $candidates,
            'mood'       => $mood,
        ];
    }

    /** 設定が無いときの安全デフォルト（最小） */
    private function defaultConf(): array
    {
        return [
            'types'   => ['craft_beer'],
            'weights' => [],
            'fixed_questions' => [],
            'categories' => [],
            'scoring' => ['q2_multiplier'=>1.0,'candidate_width'=>3.0],
            'labels'  => ['craft_beer'=>'クラフトビール'],
        ];
    }
}
