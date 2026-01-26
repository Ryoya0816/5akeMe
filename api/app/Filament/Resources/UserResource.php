<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'ユーザー管理';

    protected static ?string $modelLabel = 'ユーザー';

    protected static ?string $pluralModelLabel = 'ユーザー';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本情報')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('名前')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('メールアドレス')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('provider')
                            ->label('ログイン方法')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('権限・ステータス')
                    ->schema([
                        Forms\Components\Toggle::make('is_admin')
                            ->label('管理者権限')
                            ->helperText('ONにすると管理画面にアクセスできます'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('アカウント有効')
                            ->helperText('OFFにするとログインできなくなります')
                            ->default(true),
                        Forms\Components\Textarea::make('suspended_reason')
                            ->label('停止理由')
                            ->rows(2)
                            ->visible(fn ($get) => !$get('is_active')),
                    ])->columns(2),

                Forms\Components\Section::make('登録情報')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('登録日時')
                            ->content(fn (User $record): string => $record->created_at?->format('Y/m/d H:i') ?? '-'),
                        Forms\Components\Placeholder::make('email_verified_at')
                            ->label('メール確認日時')
                            ->content(fn (User $record): string => $record->email_verified_at?->format('Y/m/d H:i') ?? '未確認'),
                        Forms\Components\Placeholder::make('suspended_at')
                            ->label('停止日時')
                            ->content(fn (User $record): string => $record->suspended_at?->format('Y/m/d H:i') ?? '-')
                            ->visible(fn (User $record): bool => !$record->is_active),
                    ])->columns(3)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('アバター')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=U&background=random'),
                Tables\Columns\TextColumn::make('name')
                    ->label('名前')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('メールアドレス')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider')
                    ->label('ログイン')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'google' => 'Google',
                        'line' => 'LINE',
                        'twitter' => 'X',
                        default => 'メール',
                    })
                    ->color(fn ($state) => match($state) {
                        'google' => 'danger',
                        'line' => 'success',
                        'twitter' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('管理者')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('状態')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('登録日')
                    ->date('Y/m/d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->label('ログイン方法')
                    ->options([
                        'google' => 'Google',
                        'line' => 'LINE',
                        'twitter' => 'X (Twitter)',
                    ])
                    ->placeholder('すべて'),
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('管理者')
                    ->placeholder('すべて')
                    ->trueLabel('管理者のみ')
                    ->falseLabel('一般ユーザーのみ'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('アカウント状態')
                    ->placeholder('すべて')
                    ->trueLabel('有効のみ')
                    ->falseLabel('停止中のみ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('suspend')
                    ->label('停止')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('アカウントを停止しますか？')
                    ->modalDescription('このユーザーはログインできなくなります。')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('停止理由')
                            ->placeholder('スパム行為、不適切な投稿など')
                            ->rows(2),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->suspend($data['reason'] ?? null);
                    })
                    ->visible(fn (User $record) => $record->is_active && !$record->is_admin),
                Tables\Actions\Action::make('unsuspend')
                    ->label('停止解除')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('アカウント停止を解除しますか？')
                    ->action(fn (User $record) => $record->unsuspend())
                    ->visible(fn (User $record) => !$record->is_active),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('suspend')
                        ->label('一括停止')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->suspend('管理者による一括停止')),
                    Tables\Actions\BulkAction::make('unsuspend')
                        ->label('一括停止解除')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->unsuspend()),
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
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
