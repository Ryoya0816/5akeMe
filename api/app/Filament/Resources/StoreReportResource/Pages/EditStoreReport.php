<?php

namespace App\Filament\Resources\StoreReportResource\Pages;

use App\Filament\Resources\StoreReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoreReport extends EditRecord
{
    protected static string $resource = StoreReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
