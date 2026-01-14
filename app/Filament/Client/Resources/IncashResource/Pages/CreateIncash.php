<?php

namespace App\Filament\Client\Resources\IncashResource\Pages;

use App\Filament\Client\Resources\IncashResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateIncash extends CreateRecord
{
    protected static string $resource = IncashResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Инкассация создана')
            ->body('Операция инкассации успешно создана и ожидает подтверждения.');
    }
}
