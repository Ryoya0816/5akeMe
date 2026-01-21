<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = '店舗管理';

    protected static ?string $modelLabel = '店舗';

    protected static ?string $pluralModelLabel = '店舗一覧';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本情報')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('店名')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('住所')
                            ->maxLength(255)
                            ->placeholder('佐賀県佐賀市〇〇町1-2-3'),
                        Forms\Components\TextInput::make('phone')
                            ->label('電話番号')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('0952-XX-XXXX'),
                    ])->columns(1),

                Forms\Components\Section::make('営業情報')
                    ->schema([
                        Forms\Components\TextInput::make('business_hours')
                            ->label('営業時間')
                            ->maxLength(255)
                            ->placeholder('18:00〜24:00'),
                        Forms\Components\TextInput::make('closed_days')
                            ->label('定休日')
                            ->maxLength(255)
                            ->placeholder('日曜日'),
                    ])->columns(2),

                Forms\Components\Section::make('お酒情報')
                    ->schema([
                        Forms\Components\CheckboxList::make('sake_types')
                            ->label('おすすめの酒タイプ')
                            ->options(Store::sakeTypeOptions())
                            ->columns(3),
                    ]),

                Forms\Components\Section::make('雰囲気')
                    ->schema([
                        Forms\Components\Radio::make('mood')
                            ->label('お店の雰囲気')
                            ->options(Store::moodOptions())
                            ->descriptions([
                                'lively' => 'わいわい飲みたい・サクッと飲みたい・ガッツリ飲みたい人向け',
                                'calm' => 'しっとり飲みたい・一人で静かに飲みたい人向け',
                                'both' => 'どちらの気分の人にもおすすめ',
                            ])
                            ->default('both'),
                    ]),

                Forms\Components\Section::make('リンク・設定')
                    ->schema([
                        Forms\Components\TextInput::make('website_url')
                            ->label('お店のHP・SNS等')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('公開する')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('店名')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('住所')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('phone')
                    ->label('電話番号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_hours')
                    ->label('営業時間')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('closed_days')
                    ->label('定休日')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mood')
                    ->label('雰囲気')
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'lively' => '🎉 にぎやか',
                        'calm' => '🌙 落ち着き',
                        'both' => '✨ 両方OK',
                        default => '未設定',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('公開')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新日')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('公開状態')
                    ->placeholder('すべて')
                    ->trueLabel('公開中')
                    ->falseLabel('非公開'),
                Tables\Filters\TrashedFilter::make()
                    ->label('削除済み'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('編集'),
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
            ->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
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
