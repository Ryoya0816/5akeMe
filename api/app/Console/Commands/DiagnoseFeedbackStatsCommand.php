<?php

namespace App\Console\Commands;

use App\Models\DiagnoseFeedback;
use Illuminate\Console\Command;

class DiagnoseFeedbackStatsCommand extends Command
{
    protected $signature = 'diagnose:feedback-stats
                            {--detail : 低評価(1-2点)の回答スナップショットを数件表示}
                            {--limit=10 : 表示する低評価サンプル数}';

    protected $description = '診断フィードバックを集計し、精度改善のヒントを表示（評価別・結果タイプ別）';

    public function handle(): int
    {
        $total = DiagnoseFeedback::count();
        if ($total === 0) {
            $this->info('フィードバックはまだありません。');
            return self::SUCCESS;
        }

        $this->info('=== 評価別件数 ===');
        $byRating = DiagnoseFeedback::selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
            ->keyBy('rating');

        $labels = DiagnoseFeedback::ratingOptions();
        for ($r = 1; $r <= 5; $r++) {
            $count = $byRating->get($r)?->count ?? 0;
            $label = $labels[$r] ?? '';
            $this->line("  {$r}点: {$count}件  {$label}");
        }

        $lowCount = DiagnoseFeedback::whereIn('rating', [1, 2])->count();
        if ($lowCount === 0) {
            $this->newLine();
            $this->info('低評価(1-2点)はありません。');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info('=== 低評価(1-2点)の result_type 別件数（ウェイト調整の目安） ===');
        $byType = DiagnoseFeedback::whereIn('rating', [1, 2])
            ->selectRaw('result_type, COUNT(*) as count')
            ->groupBy('result_type')
            ->orderByDesc('count')
            ->get();

        foreach ($byType as $row) {
            $type = $row->result_type ?: '(null)';
            $this->line("  {$type}: {$row->count}件");
        }

        if ($this->option('detail')) {
            $this->newLine();
            $this->info('=== 低評価サンプル（回答スナップショット） ===');
            $limit = (int) $this->option('limit');
            $samples = DiagnoseFeedback::with('diagnoseResult')
                ->whereIn('rating', [1, 2])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

            foreach ($samples as $i => $fb) {
                $this->line('--- #' . ($i + 1) . ' 評価:' . $fb->rating . ' result_type:' . ($fb->result_type ?? '') . ' ---');
                $ans = $fb->answers_snapshot;
                if (is_array($ans)) {
                    $this->line(json_encode($ans, JSON_UNESCAPED_UNICODE));
                } else {
                    $this->line((string) $ans);
                }
            }
        }

        $this->newLine();
        $this->comment('ヒント: config/diagnose.php の weights で「よく低評価になる result_type × 回答パターン」の加点を下げると精度改善につながります。');
        $this->comment('詳細は docs/DIAGNOSE_ACCURACY.md を参照してください。');

        return self::SUCCESS;
    }
}
