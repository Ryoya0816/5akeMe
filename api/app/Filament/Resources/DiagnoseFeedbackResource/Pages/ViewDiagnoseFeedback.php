<?php

namespace App\Filament\Resources\DiagnoseFeedbackResource\Pages;

use App\Filament\Resources\DiagnoseFeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiagnoseFeedback extends ViewRecord
{
    protected static string $resource = DiagnoseFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
