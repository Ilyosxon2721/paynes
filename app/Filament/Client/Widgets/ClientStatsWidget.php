<?php

namespace App\Filament\Client\Widgets;

use App\Models\Branch;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $clientId = auth()->user()->client_id;
        $client = auth()->user()->client;

        $totalBranches = Branch::where('client_id', $clientId)->count();
        $activeBranches = Branch::where('client_id', $clientId)->where('status', 'active')->count();

        $totalUsers = User::where('client_id', $clientId)->where('is_client_admin', false)->count();
        $activeUsers = User::where('client_id', $clientId)->where('is_client_admin', false)->where('status', 'active')->count();

        $subscription = $client?->activeSubscription;
        $daysLeft = $subscription ? now()->diffInDays($subscription->end_date, false) : 0;

        return [
            Stat::make('Филиалы', $totalBranches)
                ->description($activeBranches . ' активных')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),

            Stat::make('Сотрудники', $totalUsers)
                ->description($activeUsers . ' активных')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Подписка', $daysLeft > 0 ? $daysLeft . ' дней' : 'Истекла')
                ->description($subscription ? 'до ' . $subscription->end_date->format('d.m.Y') : 'Нет активной подписки')
                ->descriptionIcon($daysLeft > 7 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($daysLeft > 7 ? 'success' : ($daysLeft > 0 ? 'warning' : 'danger')),
        ];
    }
}
