<?php

namespace App\Filament\Client\Resources\UserResource\Pages;

use App\Filament\Client\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $user = $this->record;

        // Синхронизируем роль с должностью
        $role = match ($user->position) {
            'manager' => 'manager',
            'cashier' => 'cashier',
            default => 'cashier',
        };

        // Удаляем старые роли (кроме admin)
        $user->roles()->whereNotIn('name', ['admin', 'client_admin'])->detach();

        // Назначаем новую роль
        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }
    }
}
