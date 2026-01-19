<?php

namespace App\Filament\Widgets;

use App\Models\DiagnoseResult;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DiagnoseTrendChart extends ChartWidget
{
    protected static ?string $heading = '診断数の推移（過去14日間）';
    
    protected static ?int $sort = 5;

    protected static ?string $maxHeight = '250px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = 14;
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('n/j');
            
            $count = DiagnoseResult::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => '診断数',
                    'data' => $data,
                    'borderColor' => '#9c3f2e',
                    'backgroundColor' => 'rgba(156, 63, 46, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
