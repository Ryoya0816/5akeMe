<?php

namespace App\Filament\Resources\DiagnoseFeedbackResource\Pages;

use App\Filament\Resources\DiagnoseFeedbackResource;
use Filament\Resources\Pages\ListRecords;

class ListDiagnoseFeedbacks extends ListRecords
{
    protected static string $resource = DiagnoseFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
