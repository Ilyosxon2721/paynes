<?php
namespace App\Filament\Client\Resources\ShiftResource\Pages;
use App\Filament\Client\Resources\ShiftResource;
use Filament\Resources\Pages\CreateRecord;
class CreateShift extends CreateRecord {
    protected static string $resource = ShiftResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        $data['cashier_id'] = auth()->id();
        $data['status'] = 'open';
        $data['opened_at'] = now();
        $data['shift_date'] = now()->toDateString();
        return $data;
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
}
