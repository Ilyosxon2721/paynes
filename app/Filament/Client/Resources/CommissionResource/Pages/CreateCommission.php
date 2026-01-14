<?php
namespace App\Filament\Client\Resources\CommissionResource\Pages;
use App\Filament\Client\Resources\CommissionResource;
use Filament\Resources\Pages\CreateRecord;
class CreateCommission extends CreateRecord {
    protected static string $resource = CommissionResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        $data['client_id'] = auth()->user()->client_id;
        return $data;
    }
}
