<?php

namespace App\Filament\Widgets;

use App\Models\DiagnoseFeedback;
use Filament\Widgets\ChartWidget;

class FeedbackRatingChart extends ChartWidget
{
    protected static ?string $heading = 'フィードバック評価分布';
    
    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        // 評価別のフィードバック数を集計
        $results = DiagnoseFeedback::selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
            ->keyBy('rating');

        $labels = ['⭐ 1', '⭐⭐ 2', '⭐⭐⭐ 3', '⭐⭐⭐⭐ 4', '⭐⭐⭐⭐⭐ 5'];
        $data = [];

        for ($i = 1; $i <= 5; $i++) {
            $data[] = $results->get($i)?->count ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'フィードバック数',
                    'data' => $data,
                    'backgroundColor' => [
                        '#ef4444', // 1: red
                        '#f97316', // 2: orange
                        '#eab308', // 3: yellow
                        '#84cc16', // 4: lime
                        '#22c55e', // 5: green
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
