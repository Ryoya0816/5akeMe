<?php

namespace App\Filament\Widgets;

use App\Models\DiagnoseResult;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestDiagnoses extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '最新の診断結果';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DiagnoseResult::query()->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('primary_label')
                    ->label('診断結果')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('mood')
                    ->label('気分')
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'lively' => '🎉 わいわい',
                        'chill' => '🍵 しっとり',
                        'silent' => '🌙 静かに',
                        'light' => '🍃 サクッと',
                        'strong' => '🔥 ガッツリ',
                        default => $state ?? '-',
                    }),

                Tables\Columns\TextColumn::make('feedback.rating')
                    ->label('評価')
                    ->formatStateUsing(fn (mixed $state): string => $state ? str_repeat('⭐', (int) $state) : '-')
                    ->default('-'),

                Tables\Columns\TextColumn::make('result_id')
                    ->label('ID')
                    ->limit(12)
                    ->tooltip(fn ($record) => $record->result_id),
            ])
            ->paginated(false);
    }
}
