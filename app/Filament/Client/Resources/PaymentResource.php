<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Платежи';

    protected static ?string $pluralModelLabel = 'платежи';

    protected static ?int $navigationSort = 20;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('Лицевой счет')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Тип платежа')
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Клиент')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Сумма')
                    ->money('UZS', divideBy: 1)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'processed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтвержден',
                        'processed' => 'Обработан',
                        'cancelled' => 'Отменен',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('cashier.full_name')
                    ->label('Кассир')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'confirmed' => 'Подтвержден',
                        'processed' => 'Обработан',
                        'cancelled' => 'Отменен',
                    ]),

                Tables\Filters\SelectFilter::make('payment_type_id')
                    ->label('Тип платежа')
                    ->relationship('paymentType', 'name'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('С'),
                        Forms\Components\DatePicker::make('created_until')->label('По'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Подтвердить')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Payment $record) => $record->status === 'pending' && auth()->user()->can('payments.confirm'))
                    ->action(function (Payment $record) {
                        $record->update(['status' => 'confirmed']);
                        \Filament\Notifications\Notification::make()->title('Платеж подтвержден')->success()->send();
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Отменить')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Payment $record) => $record->status !== 'cancelled' && auth()->user()->hasAnyRole(['client_admin', 'manager']))
                    ->action(function (Payment $record) {
                        $record->update(['status' => 'cancelled']);
                        \Filament\Notifications\Notification::make()->title('Платеж отменен')->success()->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        // Кассир видит только свои платежи
        if ($user->hasRole('cashier')) {
            $query->where('cashier_id', $user->id);
        }

        // Менеджер видит платежи кассиров своих филиалов
        if ($user->hasRole('manager')) {
            $branchIds = $user->managedBranches()->pluck('branches.id')->toArray();
            $cashierIds = \App\Models\User::whereIn('branch_id', $branchIds)
                ->where('position', 'cashier')
                ->pluck('id')
                ->toArray();
            $query->whereIn('cashier_id', $cashierIds);
        }

        // Company admin видит все платежи компании
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
        // Создание платежей через другой интерфейс (кассовая панель)
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['client_admin', 'manager', 'cashier']);
    }

    public static function canEdit($record): bool
    {
        return false; // Платежи не редактируются, только подтверждаются/отменяются
    }

    public static function canDelete($record): bool
    {
        // Только admin может удалять
        return auth()->user()->hasRole('client_admin') && $record->status === 'cancelled';
    }
}
