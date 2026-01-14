<?php

namespace App\Filament\Client\Widgets;

use App\Models\Payment;
use App\Models\Exchange;
use App\Models\Credit;
use App\Models\CashierShift;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CashierStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Проверяем, что пользователь - кассир
        if (!$user->hasRole('cashier')) {
            return [];
        }

        // Получаем текущую открытую смену
        $currentShift = CashierShift::where('cashier_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$currentShift) {
            return [
                Stat::make('Статус смены', 'Смена закрыта')
                    ->description('Откройте смену для начала работы')
                    ->descriptionIcon('heroicon-m-lock-closed')
                    ->color('danger'),

                Stat::make('Платежей за сегодня', 0)
                    ->description('Смена не открыта')
                    ->descriptionIcon('heroicon-m-currency-dollar')
                    ->color('secondary'),

                Stat::make('Обменов валют', 0)
                    ->description('Смена не открыта')
                    ->descriptionIcon('heroicon-m-arrow-path')
                    ->color('secondary'),

                Stat::make('Кредитов', 0)
                    ->description('Смена не открыта')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('secondary'),
            ];
        }

        // Статистика текущей смены
        $shiftPayments = Payment::where('cashier_id', $user->id)
            ->where('cashier_shift_id', $currentShift->id)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $shiftPaymentsCount = Payment::where('cashier_id', $user->id)
            ->where('cashier_shift_id', $currentShift->id)
            ->where('status', '!=', 'cancelled')
            ->count();

        $shiftExchanges = Exchange::where('cashier_id', $user->id)
            ->where('cashier_shift_id', $currentShift->id)
            ->count();

        $shiftCredits = Credit::where('cashier_id', $user->id)
            ->where('cashier_shift_id', $currentShift->id)
            ->count();

        $shiftDuration = now()->diffInHours($currentShift->opened_at);

        return [
            Stat::make('Статус смены', 'Смена открыта')
                ->description($shiftDuration . ' ч. работы')
                ->descriptionIcon('heroicon-m-lock-open')
                ->color('success'),

            Stat::make('Платежей в смене', $shiftPaymentsCount)
                ->description(number_format($shiftPayments, 0, ',', ' ') . ' UZS')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([3, 5, 8, 10, 12, 15, $shiftPaymentsCount]),

            Stat::make('Обменов валют', $shiftExchanges)
                ->description('За текущую смену')
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color('info'),

            Stat::make('Кредитов', $shiftCredits)
                ->description('За текущую смену')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('cashier') ?? false;
    }
}
