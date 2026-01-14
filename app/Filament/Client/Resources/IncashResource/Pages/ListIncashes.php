<?php

namespace App\Filament\Client\Resources\IncashResource\Pages;

use App\Filament\Client\Resources\IncashResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIncashes extends ListRecords
{
    protected static string $resource = IncashResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
