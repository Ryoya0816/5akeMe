<?php

namespace App\Filament\Widgets;

use App\Models\DiagnoseFeedback;
use App\Models\DiagnoseResult;
use App\Models\Store;
use App\Models\StoreReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 今日の診断数
        $todayDiagnoses = DiagnoseResult::whereDate('created_at', today())->count();
        
        // 総診断数
        $totalDiagnoses = DiagnoseResult::count();
        
        // 今週の診断数（先週比）
        $thisWeek = DiagnoseResult::whereBetween('created_at', [now()->startOfWeek(), now()])->count();
        $lastWeek = DiagnoseResult::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $weekChange = $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100) : 0;

        // フィードバック数と平均評価
        $feedbackCount = DiagnoseFeedback::count();
        $avgRating = DiagnoseFeedback::avg('rating');

        // 店舗数
        $activeStores = Store::where('is_active', true)->count();

        // 未対応の報告数
        $pendingReports = StoreReport::where('status', 'pending')->count();

        return [
            Stat::make('今日の診断', $todayDiagnoses)
                ->description('本日の診断回数')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('総診断数', number_format($totalDiagnoses))
                ->description('累計診断回数')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('今週の診断', $thisWeek)
                ->description($weekChange >= 0 ? "+{$weekChange}% 先週比" : "{$weekChange}% 先週比")
                ->descriptionIcon($weekChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($weekChange >= 0 ? 'success' : 'danger'),

            Stat::make('フィードバック', $feedbackCount)
                ->description($avgRating ? '平均 ' . number_format($avgRating, 1) . ' 点' : 'まだなし')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('登録店舗', $activeStores)
                ->description('アクティブな店舗')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),

            Stat::make('未対応報告', $pendingReports)
                ->description('要確認の報告')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($pendingReports > 0 ? 'danger' : 'success'),
        ];
    }
}
