<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\IncashResource\Pages;
use App\Models\Incash;
use App\Models\CashierShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IncashResource extends Resource
{
    protected static ?string $model = Incash::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Инкассация';

    protected static ?string $modelLabel = 'инкассация';

    protected static ?string $pluralModelLabel = 'инкассации';

    protected static ?string $navigationGroup = 'Операции';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $currentShift = CashierShift::where('cashier_id', $user->id)
            ->where('status', 'open')
            ->first();

        return $form
            ->schema([
                Forms\Components\Section::make('Данные инкассации')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Дата')
                            ->default(now())
                            ->required(),
                        Forms\Components\TimePicker::make('time')
                            ->label('Время')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('account_number')
                            ->label('Расчетный счет')
                            ->options([
                                '001' => '001 - Основной счет UZS',
                                '002' => '002 - Резервный счет UZS',
                                '003' => '003 - Комиссионный счет',
                                '840' => '840 - Валютный счет USD',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('type')
                            ->label('Тип операции')
                            ->options([
                                'income' => 'Приход',
                                'expense' => 'Расход',
                            ])
                            ->required()
                            ->default('income'),
                        Forms\Components\TextInput::make('amount')
                            ->label('Сумма')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0.01),
                        Forms\Components\Hidden::make('cashier_id')
                            ->default($user->id),
                        Forms\Components\Hidden::make('cashier_shift_id')
                            ->default($currentShift?->id),
                        Forms\Components\Hidden::make('status')
                            ->default('pending'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('Время')
                    ->time('H:i'),
                Tables\Columns\BadgeColumn::make('account_number')
                    ->label('Счет')
                    ->colors([
                        'primary' => '001',
                        'success' => '002',
                        'warning' => '003',
                        'info' => '840',
                    ]),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Тип')
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'income' => 'Приход',
                        'expense' => 'Расход',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('UZS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cashier.full_name')
                    ->label('Кассир')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтвержден',
                        'cancelled' => 'Отменен',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип')
                    ->options([
                        'income' => 'Приход',
                        'expense' => 'Расход',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтвержден',
                        'cancelled' => 'Отменен',
                    ]),
                Tables\Filters\SelectFilter::make('account_number')
                    ->label('Счет')
                    ->options([
                        '001' => '001',
                        '002' => '002',
                        '003' => '003',
                        '840' => '840',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Подтвердить')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Incash $record) => $record->update(['status' => 'confirmed']))
                    ->visible(fn (Incash $record) => $record->status === 'pending' && auth()->user()->hasAnyRole(['client_admin', 'manager'])),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Incash $record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListIncashes::route('/'),
            'create' => Pages\CreateIncash::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        // Фильтр по компании
        $clientUserIds = $user->client?->users()->pluck('id')->toArray() ?? [$user->id];
        $query->whereIn('cashier_id', $clientUserIds);

        // Кассир видит только свои операции
        if ($user->hasRole('cashier')) {
            $query->where('cashier_id', $user->id);
        }

        // Менеджер видит операции своих филиалов
        if ($user->hasRole('manager')) {
            $branchIds = $user->managedBranches()->pluck('branches.id')->toArray();
            $cashierIds = \App\Models\User::whereIn('branch_id', $branchIds)->pluck('id')->toArray();
            $query->whereIn('cashier_id', $cashierIds);
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['client_admin', 'manager', 'cashier']);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        // Кассир может создавать только при открытой смене
        if ($user->hasRole('cashier')) {
            return CashierShift::where('cashier_id', $user->id)
                ->where('status', 'open')
                ->exists();
        }

        return $user->hasAnyRole(['client_admin', 'manager']);
    }
}
