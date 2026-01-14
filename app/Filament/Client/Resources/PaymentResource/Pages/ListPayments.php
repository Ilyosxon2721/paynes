<?php
namespace App\Filament\Client\Resources\PaymentResource\Pages;
use App\Filament\Client\Resources\PaymentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
class ListPayments extends ListRecords {
    protected static string $resource = PaymentResource::class;
    
    public function getTabs(): array {
        return [
            'all' => Tab::make('Все'),
            'pending' => Tab::make('Ожидают')->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))->badge(fn () => $this->getModel()::where('status', 'pending')->count()),
            'confirmed' => Tab::make('Подтвержденные')->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed')),
            'processed' => Tab::make('Обработанные')->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processed')),
        ];
    }
}
