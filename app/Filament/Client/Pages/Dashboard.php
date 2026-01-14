<?php

namespace App\Filament\Client\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.client.pages.dashboard';

    public function getWidgets(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // Управляющий компании видит полную статистику
        if ($user->hasRole('client_admin')) {
            return [
                \App\Filament\Client\Widgets\CompanyStatsOverview::class,
            ];
        }

        // Менеджер видит статистику по своим филиалам
        if ($user->hasRole('manager')) {
            return [
                \App\Filament\Client\Widgets\ManagerStatsOverview::class,
            ];
        }

        // Кассир видит статистику своей смены
        if ($user->hasRole('cashier')) {
            return [
                \App\Filament\Client\Widgets\CashierStatsOverview::class,
            ];
        }

        return [];
    }

    public function getTitle(): string
    {
        $user = Auth::user();

        if ($user->hasRole('client_admin')) {
            return 'Панель управления компанией';
        }

        if ($user->hasRole('manager')) {
            return 'Панель менеджера';
        }

        if ($user->hasRole('cashier')) {
            return 'Кассовая панель';
        }

        return 'Главная';
    }

    public function getSubheading(): ?string
    {
        $user = Auth::user();

        if ($user->client) {
            return $user->client->company_name;
        }

        return null;
    }
}
