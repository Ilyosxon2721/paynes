<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Удаляем поля админа из данных клиента
        unset(
            $data['admin_login'],
            $data['admin_full_name'],
            $data['admin_email'],
            $data['admin_phone'],
            $data['admin_password']
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $formData = $this->form->getState();

        // Создаём администратора компании
        $admin = User::create([
            'login' => $formData['admin_login'],
            'full_name' => $formData['admin_full_name'],
            'email' => $formData['admin_email'] ?? null,
            'phone' => $formData['admin_phone'] ?? null,
            'password' => Hash::make($formData['admin_password']),
            'position' => 'client_admin',
            'status' => 'active',
            'client_id' => $this->record->id,
            'is_client_admin' => true,
        ]);

        // Назначаем роль
        $admin->assignRole('client_admin');

        Notification::make()
            ->title('Администратор создан')
            ->body("Логин: {$admin->login}")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
