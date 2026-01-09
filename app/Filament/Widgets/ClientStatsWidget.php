<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $activeClients = Client::where('status', 'active')->count();
        $totalClients = Client::count();

        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('end_date', '>=', now())
            ->count();

        $expiringSubscriptions = Subscription::where('status', 'active')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->count();

        $monthlyRevenue = Subscription::where('status', 'active')
            ->where('end_date', '>=', now())
            ->sum('monthly_price');

        return [
            Stat::make('Всего клиентов', $totalClients)
                ->description($activeClients . ' активных')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Активные подписки', $activeSubscriptions)
                ->description($expiringSubscriptions . ' заканчиваются скоро')
                ->descriptionIcon($expiringSubscriptions > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($expiringSubscriptions > 0 ? 'warning' : 'success'),

            Stat::make('Ежемесячный доход', number_format($monthlyRevenue, 0, '.', ' ') . ' UZS')
                ->description('От активных подписок')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
