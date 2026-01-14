<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ShiftResource\Pages;
use App\Models\CashierShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShiftResource extends Resource
{
    protected static ?string $model = CashierShift::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Смены';

    protected static ?string $modelLabel = 'смена';

    protected static ?string $pluralModelLabel = 'смены';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Открытие смены')
                    ->schema([
                        Forms\Components\DatePicker::make('shift_date')
                            ->label('Дата смены')
                            ->default(now())
                            ->required()
                            ->disabled(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('opening_cash_uzs')
                                    ->label('Наличные UZS')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('UZS')
                                    ->required(),

                                Forms\Components\TextInput::make('opening_cash_usd')
                                    ->label('Наличные USD')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('USD')
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('opening_cashless_uzs')
                                    ->label('Безнал UZS')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('UZS'),

                                Forms\Components\TextInput::make('opening_card_uzs')
                                    ->label('Карта UZS')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('UZS'),

                                Forms\Components\TextInput::make('opening_p2p_uzs')
                                    ->label('P2P UZS')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('UZS'),
                            ]),

                        Forms\Components\Textarea::make('opening_notes')
                            ->label('Примечания при открытии')
                            ->rows(2),
                    ])
                    ->visible(fn ($record) => !$record || $record->status === 'open'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shift_date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cashier.full_name')
                    ->label('Кассир')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'success' => 'open',
                        'secondary' => 'closed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Открыта',
                        'closed' => 'Закрыта',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('opened_at')
                    ->label('Открыта в')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('closed_at')
                    ->label('Закрыта в')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('opening_cash_uzs')
                    ->label('Начальная сумма')
                    ->money('UZS', divideBy: 1)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('closing_cash_uzs')
                    ->label('Конечная сумма')
                    ->money('UZS', divideBy: 1)
                    ->toggleable()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'open' => 'Открыта',
                        'closed' => 'Закрыта',
                    ]),

                Tables\Filters\SelectFilter::make('cashier_id')
                    ->label('Кассир')
                    ->relationship('cashier', 'full_name')
                    ->visible(fn () => auth()->user()->hasAnyRole(['client_admin', 'manager'])),
            ])
            ->actions([
                Tables\Actions\Action::make('close_shift')
                    ->label('Закрыть смену')
                    ->icon('heroicon-m-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CashierShift $record) => $record->status === 'open')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('closing_cash_uzs')
                                    ->label('Наличные UZS')
                                    ->numeric()
                                    ->required()
                                    ->suffix('UZS'),

                                Forms\Components\TextInput::make('closing_cash_usd')
                                    ->label('Наличные USD')
                                    ->numeric()
                                    ->required()
                                    ->suffix('USD'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('closing_cashless_uzs')
                                    ->label('Безнал UZS')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('UZS'),

                                Forms\Components\TextInput::make('closing_card_uzs')
                                    ->label('Карта UZS')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('UZS'),

                                Forms\Components\TextInput::make('closing_p2p_uzs')
                                    ->label('P2P UZS')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('UZS'),
                            ]),

                        Forms\Components\Textarea::make('closing_notes')
                            ->label('Примечания при закрытии')
                            ->rows(2),
                    ])
                    ->action(function (CashierShift $record, array $data) {
                        $record->update([
                            'status' => 'closed',
                            'closed_at' => now(),
                            'closing_cash_uzs' => $data['closing_cash_uzs'],
                            'closing_cash_usd' => $data['closing_cash_usd'],
                            'closing_cashless_uzs' => $data['closing_cashless_uzs'] ?? 0,
                            'closing_card_uzs' => $data['closing_card_uzs'] ?? 0,
                            'closing_p2p_uzs' => $data['closing_p2p_uzs'] ?? 0,
                            'closing_notes' => $data['closing_notes'] ?? null,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Смена закрыта')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('shift_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'view' => Pages\ViewShift::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        // Кассир видит только свои смены
        if ($user->hasRole('cashier')) {
            $query->where('cashier_id', $user->id);
        }

        // Менеджер видит смены кассиров своих филиалов
        if ($user->hasRole('manager')) {
            $branchIds = $user->managedBranches()->pluck('branches.id')->toArray();
            $cashierIds = \App\Models\User::whereIn('branch_id', $branchIds)
                ->where('position', 'cashier')
                ->pluck('id')
                ->toArray();
            $query->whereIn('cashier_id', $cashierIds);
        }

        // Company admin видит все смены компании
        if ($user->hasRole('client_admin')) {
            $cashierIds = $user->client->users()
                ->where('position', 'cashier')
                ->pluck('id')
                ->toArray();
            $query->whereIn('cashier_id', $cashierIds);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        // Только кассир может открывать смену
        if (!$user->hasRole('cashier')) {
            return false;
        }

        // Проверяем, нет ли открытой смены
        $hasOpenShift = CashierShift::where('cashier_id', $user->id)
            ->where('status', 'open')
            ->exists();

        return !$hasOpenShift;
    }

    public static function canViewAny(): bool
    {
        // Все роли могут просматривать смены
        return auth()->user()->hasAnyRole(['client_admin', 'manager', 'cashier']);
    }

    public static function canEdit($record): bool
    {
        // Смены нельзя редактировать, только открывать и закрывать
        return false;
    }

    public static function canDelete($record): bool
    {
        // Только admin может удалять смены
        return auth()->user()->hasRole('client_admin') && $record->status === 'closed';
    }
}
