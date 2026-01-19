<?php

namespace App\Filament\Widgets;

use App\Models\DiagnoseResult;
use Filament\Widgets\ChartWidget;

class MoodChart extends ChartWidget
{
    protected static ?string $heading = '気分（Mood）別 診断結果';
    
    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // mood別の診断数を集計
        $results = DiagnoseResult::selectRaw('mood, COUNT(*) as count')
            ->whereNotNull('mood')
            ->groupBy('mood')
            ->orderByDesc('count')
            ->get();

        $moodLabels = [
            'lively' => '🎉 わいわい',
            'chill'  => '🍵 しっとり',
            'silent' => '🌙 静かに',
            'light'  => '🍃 サクッと',
            'strong' => '🔥 ガッツリ',
        ];

        return [
            'datasets' => [
                [
                    'label' => '診断数',
                    'data' => $results->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#f59e0b', // amber
                        '#8b5cf6', // violet
                        '#3b82f6', // blue
                        '#10b981', // emerald
                        '#ef4444', // red
                    ],
                ],
            ],
            'labels' => $results->map(function ($item) use ($moodLabels) {
                return $moodLabels[$item->mood] ?? $item->mood;
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
