<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use App\Models\Store;
use Filament\Resources\Pages\CreateRecord;

class CreateStore extends CreateRecord
{
    protected static string $resource = StoreResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['business_hours'] = Store::combineBusinessHours(
            $data['business_hours_open'] ?? null,
            $data['business_hours_close'] ?? null
        );
        unset($data['business_hours_open'], $data['business_hours_close']);

        return $data;
    }
}
