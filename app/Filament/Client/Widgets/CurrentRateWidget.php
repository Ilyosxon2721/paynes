<?php

namespace App\Filament\Client\Widgets;

use App\Models\Rate;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CurrentRateWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $rate = Rate::getLatest();

        if (!$rate) {
            return [
                Stat::make('Курс валют', 'Не установлен')
                    ->description('Добавьте курс валют')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        return [
            Stat::make('Покупка USD', number_format($rate->buy_rate, 0, ',', ' ') . ' UZS')
                ->description('Мы покупаем доллары')
                ->descriptionIcon('heroicon-m-arrow-down-circle')
                ->color('success'),

            Stat::make('Продажа USD', number_format($rate->sell_rate, 0, ',', ' ') . ' UZS')
                ->description('Мы продаём доллары')
                ->descriptionIcon('heroicon-m-arrow-up-circle')
                ->color('danger'),

            Stat::make('Спред', number_format($rate->sell_rate - $rate->buy_rate, 0, ',', ' ') . ' UZS')
                ->description('Разница курсов')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['client_admin', 'manager', 'cashier']) ?? false;
    }
}
