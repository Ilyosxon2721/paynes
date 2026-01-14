<?php

namespace App\Filament\Client\Resources\UserResource\Pages;

use App\Filament\Client\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['client_id'] = auth()->user()->client_id;
        $data['is_client_admin'] = false;
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;

        // Автоматически назначаем роль в зависимости от должности
        $role = match ($user->position) {
            'manager' => 'manager',
            'cashier' => 'cashier',
            default => 'cashier',
        };

        $user->assignRole($role);
    }
}
