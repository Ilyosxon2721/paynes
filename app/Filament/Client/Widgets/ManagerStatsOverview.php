<?php

namespace App\Filament\Client\Widgets;

use App\Models\Payment;
use App\Models\User;
use App\Models\Exchange;
use App\Models\CashierShift;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ManagerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Проверяем, что пользователь - менеджер
        if (!$user->hasRole('manager')) {
            return [];
        }

        // Получаем филиалы менеджера
        $branchIds = $user->managedBranches()->pluck('branches.id')->toArray();

        if (empty($branchIds)) {
            return [
                Stat::make('Филиалы', 0)
                    ->description('Филиалы не назначены')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        // Получаем кассиров назначенных филиалов
        $cashierIds = User::whereIn('branch_id', $branchIds)
            ->where('position', 'cashier')
            ->pluck('id')
            ->toArray();

        // Статистика за сегодня
        $todayPayments = Payment::whereIn('cashier_id', $cashierIds)
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $todayPaymentsCount = Payment::whereIn('cashier_id', $cashierIds)
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->count();

        // Открытые смены
        $openShifts = CashierShift::whereIn('cashier_id', $cashierIds)
            ->where('status', 'open')
            ->count();

        // Кассиры в филиалах
        $cashiersCount = count($cashierIds);

        // Обмены валют за сегодня
        $todayExchanges = Exchange::whereIn('cashier_id', $cashierIds)
            ->whereDate('created_at', today())
            ->count();

        return [
            Stat::make('Платежей сегодня', $todayPaymentsCount)
                ->description(number_format($todayPayments, 0, ',', ' ') . ' UZS')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Мои филиалы', count($branchIds))
                ->description($cashiersCount . ' кассиров')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Открытых смен', $openShifts)
                ->description('Сейчас работают')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Обменов валют', $todayExchanges)
                ->description('За сегодня')
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color('primary'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('manager') ?? false;
    }
}
