<?php

namespace App\Filament\Resources\StoreReportResource\Pages;

use App\Filament\Resources\StoreReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoreReports extends ListRecords
{
    protected static string $resource = StoreReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 報告は管理者が作成するものではないので、作成ボタンは不要
        ];
    }
}
