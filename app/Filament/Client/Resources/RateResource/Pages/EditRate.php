<?php

namespace App\Filament\Client\Resources\RateResource\Pages;

use App\Filament\Client\Resources\RateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRate extends EditRecord
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
