<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Подписки';

    protected static ?string $modelLabel = 'подписка';

    protected static ?string $pluralModelLabel = 'подписки';

    protected static ?string $navigationGroup = 'Управление подписками';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Клиент')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Клиент')
                            ->relationship('client', 'company_name')
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Название компании')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Телефон'),
                            ]),
                    ]),

                Forms\Components\Section::make('Информация о подписке')
                    ->schema([
                        Forms\Components\TextInput::make('plan_name')
                            ->label('Название плана')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('monthly_price')
                            ->label('Ежемесячная цена')
                            ->required()
                            ->numeric()
                            ->prefix('UZS')
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Даты')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Дата начала')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Дата окончания')
                            ->required()
                            ->default(now()->addMonth()),
                        Forms\Components\DatePicker::make('next_billing_date')
                            ->label('Следующая дата оплаты')
                            ->default(now()->addMonth()),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Лимиты и настройки')
                    ->schema([
                        Forms\Components\TextInput::make('max_users')
                            ->label('Макс. пользователей')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->minValue(1),
                        Forms\Components\TextInput::make('max_branches')
                            ->label('Макс. филиалов')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'active' => 'Активна',
                                'expired' => 'Истекла',
                                'cancelled' => 'Отменена',
                                'suspended' => 'Приостановлена',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\Toggle::make('auto_renew')
                            ->label('Автоматическое продление')
                            ->default(true),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Примечания')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Примечания')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.company_name')
                    ->label('Клиент')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan_name')
                    ->label('План')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monthly_price')
                    ->label('Цена/месяц')
                    ->money('UZS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Начало')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Окончание')
                    ->date('d.m.Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpiringSoon() ? 'danger' : null),
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Осталось дней')
                    ->sortable()
                    ->color(fn ($record) => $record->days_remaining <= 7 ? 'danger' : 'success'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'expired',
                        'warning' => fn ($state) => in_array($state, ['cancelled', 'suspended']),
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Активна',
                        'expired' => 'Истекла',
                        'cancelled' => 'Отменена',
                        'suspended' => 'Приостановлена',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('auto_renew')
                    ->label('Авто-продление')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_users')
                    ->label('Макс. польз.')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_branches')
                    ->label('Макс. филиалов')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активна',
                        'expired' => 'Истекла',
                        'cancelled' => 'Отменена',
                        'suspended' => 'Приостановлена',
                    ]),
                Tables\Filters\SelectFilter::make('client')
                    ->label('Клиент')
                    ->relationship('client', 'company_name')
                    ->searchable(),
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Заканчиваются скоро')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')
                        ->where('end_date', '>=', now())
                        ->where('end_date', '<=', now()->addDays(7))),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('renew')
                    ->label('Продлить')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->renew())
                    ->visible(fn ($record) => $record->status === 'active'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('end_date', 'asc');
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
