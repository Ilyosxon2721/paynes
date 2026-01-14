<?php

namespace App\Filament\Client\Resources\MerchantResource\Pages;

use App\Filament\Client\Resources\MerchantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMerchant extends CreateRecord
{
    protected static string $resource = MerchantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['client_id'] = auth()->user()->client_id;
        return $data;
    }
}
