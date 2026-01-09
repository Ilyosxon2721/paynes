<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Все'),
            'active' => Tab::make('Активные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')
                    ->where('end_date', '>=', now())),
            'expiring' => Tab::make('Заканчиваются')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')
                    ->where('end_date', '>=', now())
                    ->where('end_date', '<=', now()->addDays(7))),
            'expired' => Tab::make('Истекшие')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'expired')),
        ];
    }
}
