<?php
namespace App\Filament\Client\Resources\ShiftResource\Pages;
use App\Filament\Client\Resources\ShiftResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
class ViewShift extends ViewRecord {
    protected static string $resource = ShiftResource::class;
    
    public function infolist(Infolist $infolist): Infolist {
        return $infolist->schema([
            Infolists\Components\Section::make('Информация о смене')->schema([
                Infolists\Components\TextEntry::make('cashier.full_name')->label('Кассир'),
                Infolists\Components\TextEntry::make('shift_date')->label('Дата')->date('d.m.Y'),
                Infolists\Components\TextEntry::make('status')->label('Статус')
                    ->badge()->color(fn ($state) => $state === 'open' ? 'success' : 'secondary')
                    ->formatStateUsing(fn ($state) => $state === 'open' ? 'Открыта' : 'Закрыта'),
                Infolists\Components\TextEntry::make('opened_at')->label('Открыта')->dateTime('d.m.Y H:i'),
                Infolists\Components\TextEntry::make('closed_at')->label('Закрыта')->dateTime('d.m.Y H:i')->placeholder('—'),
            ])->columns(2),
            
            Infolists\Components\Section::make('Начальные суммы')->schema([
                Infolists\Components\TextEntry::make('opening_cash_uzs')->label('Наличные UZS')->money('UZS'),
                Infolists\Components\TextEntry::make('opening_cash_usd')->label('Наличные USD')->money('USD'),
                Infolists\Components\TextEntry::make('opening_cashless_uzs')->label('Безнал UZS')->money('UZS'),
                Infolists\Components\TextEntry::make('opening_card_uzs')->label('Карта UZS')->money('UZS'),
            ])->columns(4),
            
            Infolists\Components\Section::make('Конечные суммы')->schema([
                Infolists\Components\TextEntry::make('closing_cash_uzs')->label('Наличные UZS')->money('UZS')->placeholder('—'),
                Infolists\Components\TextEntry::make('closing_cash_usd')->label('Наличные USD')->money('USD')->placeholder('—'),
                Infolists\Components\TextEntry::make('closing_cashless_uzs')->label('Безнал UZS')->money('UZS')->placeholder('—'),
                Infolists\Components\TextEntry::make('closing_card_uzs')->label('Карта UZS')->money('UZS')->placeholder('—'),
            ])->columns(4)->visible(fn ($record) => $record->status === 'closed'),
        ]);
    }
}
