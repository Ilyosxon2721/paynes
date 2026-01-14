<?php

namespace App\Filament\Client\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestPaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Последние платежи';

    public function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->query(
                Payment::query()
                    ->when($user->hasRole('cashier'), function ($query) use ($user) {
                        $query->where('cashier_id', $user->id);
                    })
                    ->when($user->hasRole('manager'), function ($query) use ($user) {
                        $branchIds = $user->managedBranches()->pluck('branches.id')->toArray();
                        $cashierIds = \App\Models\User::whereIn('branch_id', $branchIds)->pluck('id')->toArray();
                        $query->whereIn('cashier_id', $cashierIds);
                    })
                    ->when($user->hasRole('client_admin'), function ($query) use ($user) {
                        $clientUserIds = $user->client?->users()->pluck('id')->toArray() ?? [$user->id];
                        $query->whereIn('cashier_id', $clientUserIds);
                    })
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#'),
                Tables\Columns\TextColumn::make('payer_name')
                    ->label('Плательщик')
                    ->limit(20),
                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Тип')
                    ->limit(15),
                Tables\Columns\TextColumn::make('total')
                    ->label('Сумма')
                    ->money('UZS'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Время')
                    ->since(),
            ])
            ->paginated(false);
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['client_admin', 'manager', 'cashier']) ?? false;
    }
}
