<?php

namespace App\Filament\Pages;

use App\Models\DiagnoseFeedback;
use App\Models\DiagnoseResult;
use App\Models\Store;
use App\Models\StoreReport;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    /**
     * ヘッダーにリセットボタンを追加
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete_all')
                ->label('🗑️ 診断結果を削除')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('診断結果の削除')
                ->modalDescription('すべての診断結果を論理削除します（後から復元可能）。本当によろしいですか？')
                ->modalSubmitActionLabel('削除する')
                ->action(function () {
                    // DiagnoseResult の削除
                    $resultCount = DiagnoseResult::count();
                    DiagnoseResult::query()->delete();

                    if ($resultCount > 0) {
                        Notification::make()
                            ->title('削除完了')
                            ->body("{$resultCount}件の診断結果を削除しました")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('削除対象なし')
                            ->body('診断結果がありませんでした')
                            ->info()
                            ->send();
                    }
                }),

            Action::make('restore_all')
                ->label('🔄 削除データを復元')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('削除データの復元')
                ->modalDescription('論理削除されたすべてのデータを復元します。本当によろしいですか？')
                ->modalSubmitActionLabel('復元する')
                ->action(function () {
                    $restoredCount = 0;

                    // DiagnoseResult の復元
                    $resultCount = DiagnoseResult::onlyTrashed()->count();
                    DiagnoseResult::onlyTrashed()->restore();
                    $restoredCount += $resultCount;

                    // Store の復元
                    $storeCount = Store::onlyTrashed()->count();
                    Store::onlyTrashed()->restore();
                    $restoredCount += $storeCount;

                    // StoreReport の復元
                    $reportCount = StoreReport::onlyTrashed()->count();
                    StoreReport::onlyTrashed()->restore();
                    $restoredCount += $reportCount;

                    // DiagnoseFeedback の復元
                    $feedbackCount = DiagnoseFeedback::onlyTrashed()->count();
                    DiagnoseFeedback::onlyTrashed()->restore();
                    $restoredCount += $feedbackCount;

                    if ($restoredCount > 0) {
                        Notification::make()
                            ->title('復元完了')
                            ->body("{$restoredCount}件のデータを復元しました（診断結果: {$resultCount}件、店舗: {$storeCount}件、報告: {$reportCount}件、フィードバック: {$feedbackCount}件）")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('復元対象なし')
                            ->body('削除されたデータはありませんでした')
                            ->info()
                            ->send();
                    }
                }),
        ];
    }
}
