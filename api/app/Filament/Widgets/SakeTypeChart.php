<?php

namespace App\Filament\Widgets;

use App\Models\DiagnoseResult;
use Filament\Widgets\ChartWidget;

class SakeTypeChart extends ChartWidget
{
    protected static ?string $heading = 'お酒タイプ別 診断結果';
    
    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // お酒タイプ別の診断数を集計
        $results = DiagnoseResult::selectRaw('primary_type, COUNT(*) as count')
            ->groupBy('primary_type')
            ->orderByDesc('count')
            ->get();

        $labels = config('diagnose.labels', []);

        return [
            'datasets' => [
                [
                    'label' => '診断数',
                    'data' => $results->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#9c3f2e', // brand-main
                        '#d97706', // amber
                        '#059669', // emerald
                        '#7c3aed', // violet
                        '#db2777', // pink
                        '#0891b2', // cyan
                        '#65a30d', // lime
                        '#ea580c', // orange
                        '#4f46e5', // indigo
                        '#dc2626', // red
                    ],
                ],
            ],
            'labels' => $results->map(function ($item) use ($labels) {
                return $labels[$item->primary_type] ?? $item->primary_type;
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
            ],
        ];
    }
}
