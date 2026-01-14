<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\MerchantResource\Pages;
use App\Models\Merchant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MerchantResource extends Resource
{
    protected static ?string $model = Merchant::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Мерчанты';

    protected static ?string $modelLabel = 'мерчант';

    protected static ?string $pluralModelLabel = 'мерчанты';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Настройки';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название мерчанта')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Основной шлюз'),

                        Forms\Components\TextInput::make('code')
                            ->label('Код')
                            ->maxLength(50)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Генерируется автоматически')
                            ->helperText('Уникальный код генерируется автоматически'),

                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'active' => 'Активный',
                                'inactive' => 'Неактивный',
                                'suspended' => 'Приостановлен',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('API интеграция')
                    ->schema([
                        Forms\Components\TextInput::make('api_key')
                            ->label('API ключ')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Генерируется автоматически')
                            ->helperText('API ключ для интеграции'),

                        Forms\Components\TextInput::make('callback_url')
                            ->label('Webhook URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://your-site.com/webhook')
                            ->helperText('URL для получения уведомлений о платежах'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Безопасность')
                    ->schema([
                        Forms\Components\TagsInput::make('allowed_ips')
                            ->label('Разрешенные IP адреса')
                            ->placeholder('Введите IP и нажмите Enter')
                            ->helperText('Оставьте пустым для разрешения всех IP адресов'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Дополнительно')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('api_key')
                    ->label('API ключ')
                    ->copyable()
                    ->toggleable()
                    ->limit(20)
                    ->placeholder('Не задан'),

                Tables\Columns\TextColumn::make('callback_url')
                    ->label('Webhook URL')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'suspended',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Активный',
                        'inactive' => 'Неактивный',
                        'suspended' => 'Приостановлен',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активный',
                        'inactive' => 'Неактивный',
                        'suspended' => 'Приостановлен',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('regenerate_credentials')
                    ->label('Обновить ключи')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Обновить API ключи')
                    ->modalDescription('Вы уверены? Старые ключи перестанут работать.')
                    ->action(function (Merchant $record) {
                        $record->regenerateApiCredentials();
                        \Filament\Notifications\Notification::make()
                            ->title('API ключи обновлены')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
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
            'index' => Pages\ListMerchants::route('/'),
            'create' => Pages\CreateMerchant::route('/create'),
            'edit' => Pages\EditMerchant::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('client_id', auth()->user()->client_id);
    }

    public static function canViewAny(): bool
    {
        // Только client_admin может управлять мерчантами
        return auth()->user()->hasRole('client_admin');
    }
}
