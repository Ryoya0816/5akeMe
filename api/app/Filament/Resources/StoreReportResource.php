<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreReportResource\Pages;
use App\Models\StoreReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreReportResource extends Resource
{
    protected static ?string $model = StoreReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = '情報更新報告';

    protected static ?string $modelLabel = '報告';

    protected static ?string $pluralModelLabel = '情報更新報告';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('報告内容')
                    ->schema([
                        Forms\Components\Select::make('store_id')
                            ->label('店舗')
                            ->relationship('store', 'name')
                            ->required()
                            ->disabled(),

                        Forms\Components\CheckboxList::make('update_types')
                            ->label('報告種別')
                            ->options(StoreReport::updateTypeOptions())
                            ->columns(3)
                            ->disabled(),

                        Forms\Components\Textarea::make('detail')
                            ->label('詳細内容')
                            ->rows(4)
                            ->disabled(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('対応状況')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('ステータス')
                            ->options(StoreReport::statusOptions())
                            ->required(),

                        Forms\Components\Textarea::make('admin_note')
                            ->label('管理者メモ')
                            ->placeholder('対応内容や備考を記入')
                            ->rows(3),
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

                Tables\Columns\TextColumn::make('store.name')
                    ->label('店舗名')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('update_types')
                    ->label('報告種別')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) return '-';
                        // JSON文字列の場合はデコード
                        if (is_string($state)) {
                            $state = json_decode($state, true) ?? [];
                        }
                        if (!is_array($state)) return '-';
                        return implode(', ', $state);
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('detail')
                    ->label('詳細')
                    ->limit(40)
                    ->tooltip(function ($record) {
                        return $record->detail;
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('ステータス')
                    ->formatStateUsing(fn (string $state): string => StoreReport::statusOptions()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewed' => 'info',
                        'resolved' => 'success',
                        'dismissed' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('報告日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('ステータス')
                    ->options(StoreReport::statusOptions()),

                Tables\Filters\SelectFilter::make('store_id')
                    ->label('店舗')
                    ->relationship('store', 'name'),

                Tables\Filters\TrashedFilter::make()
                    ->label('削除済み'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_store')
                    ->label('店舗を見る')
                    ->icon('heroicon-o-building-storefront')
                    ->url(fn (StoreReport $record): string => route('store.detail', $record->store_id))
                    ->openUrlInNewTab(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStoreReports::route('/'),
            'edit' => Pages\EditStoreReport::route('/{record}/edit'),
        ];
    }

    /**
     * 未対応の報告数をバッジで表示
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
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
