<?php

namespace App\Filament\Client\Widgets;

use App\Models\Branch;
use App\Models\Payment;
use App\Models\User;
use App\Models\Exchange;
use App\Models\Credit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CompanyStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            return [];
        }

        // Получаем всех пользователей компании
        $userIds = $client->users()->pluck('id')->toArray();

        // Статистика за сегодня
        $todayPayments = Payment::whereIn('cashier_id', $userIds)
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $todayPaymentsCount = Payment::whereIn('cashier_id', $userIds)
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->count();

        // Статистика за месяц
        $monthPayments = Payment::whereIn('cashier_id', $userIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        // Количество активных филиалов
        $activeBranches = Branch::where('client_id', $client->id)
            ->where('status', 'active')
            ->count();

        // Количество активных пользователей
        $activeUsers = User::where('client_id', $client->id)
            ->where('status', 'active')
            ->count();

        // Обмены валют за сегодня
        $todayExchanges = Exchange::whereIn('cashier_id', $userIds)
            ->whereDate('created_at', today())
            ->count();

        return [
            Stat::make('Платежей сегодня', $todayPaymentsCount)
                ->description(number_format($todayPayments, 0, ',', ' ') . ' UZS')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([7, 12, 8, 15, 10, 18, $todayPaymentsCount]),

            Stat::make('Оборот за месяц', number_format($monthPayments, 0, ',', ' ') . ' UZS')
                ->description('Все операции компании')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Активных филиалов', $activeBranches)
                ->description($client->getRemainingBranchSlots() . ' доступно в тарифе')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Активных сотрудников', $activeUsers)
                ->description($client->getRemainingUserSlots() . ' доступно в тарифе')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
