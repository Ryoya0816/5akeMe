<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use App\Models\Store;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStore extends EditRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        $data = $this->record->getAttributes();
        [$open, $close] = Store::parseBusinessHours($data['business_hours'] ?? null);
        $data['business_hours_open'] = $open;
        $data['business_hours_close'] = $close;
        $this->form->fill($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['business_hours'] = Store::combineBusinessHours(
            $data['business_hours_open'] ?? null,
            $data['business_hours_close'] ?? null
        );
        unset($data['business_hours_open'], $data['business_hours_close']);

        return $data;
    }
}
