<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiagnoseFeedbackResource\Pages;
use App\Models\DiagnoseFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiagnoseFeedbackResource extends Resource
{
    protected static ?string $model = DiagnoseFeedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'フィードバック';

    protected static ?string $modelLabel = 'フィードバック';

    protected static ?string $pluralModelLabel = 'フィードバック一覧';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('フィードバック詳細')
                    ->schema([
                        Forms\Components\TextInput::make('rating')
                            ->label('評価')
                            ->disabled(),

                        Forms\Components\Textarea::make('comment')
                            ->label('コメント')
                            ->disabled()
                            ->rows(3),

                        Forms\Components\TextInput::make('result_type')
                            ->label('診断結果タイプ')
                            ->disabled(),

                        Forms\Components\TextInput::make('mood')
                            ->label('気分')
                            ->disabled(),

                        Forms\Components\Textarea::make('answers_snapshot')
                            ->label('回答パターン')
                            ->disabled()
                            ->rows(5)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('評価')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('result_type')
                    ->label('診断結果')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mood')
                    ->label('気分')
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'lively' => '🎉 わいわい',
                        'chill' => '🍵 しっとり',
                        'silent' => '🌙 静かに',
                        'light' => '🍃 サクッと',
                        'strong' => '🔥 ガッツリ',
                        default => $state ?? '-',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->label('コメント')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->comment)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('評価')
                    ->options([
                        1 => '⭐ 1点',
                        2 => '⭐⭐ 2点',
                        3 => '⭐⭐⭐ 3点',
                        4 => '⭐⭐⭐⭐ 4点',
                        5 => '⭐⭐⭐⭐⭐ 5点',
                    ]),

                Tables\Filters\SelectFilter::make('mood')
                    ->label('気分')
                    ->options([
                        'lively' => 'わいわい',
                        'chill' => 'しっとり',
                        'silent' => '静かに',
                        'light' => 'サクッと',
                        'strong' => 'ガッツリ',
                    ]),

                Tables\Filters\TrashedFilter::make()
                    ->label('削除済み'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('削除'),
                Tables\Actions\RestoreAction::make()
                    ->label('復元'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('完全削除'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('一括削除'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('一括復元'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('一括完全削除'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiagnoseFeedbacks::route('/'),
            'view' => Pages\ViewDiagnoseFeedback::route('/{record}'),
        ];
    }

    /**
     * 統計情報をナビゲーションバッジに表示
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? (string) $count : null;
    }

    /**
     * 論理削除されたレコードも含めて取得できるようにする
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
