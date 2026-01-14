<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание или обновление администратора
        $admin = User::firstOrCreate(
            ['login' => 'admin'],
            [
                'full_name' => 'Администратор системы',
                'password' => Hash::make('admin123'),
                'position' => 'admin',
                'status' => 'active',
                'branch' => 'Коканд глав',
                'reward_percentage' => 0,
            ]
        );

        // Назначение роли администратора (если ещё не назначена)
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Создание или обновление тестового кассира
        $cashier = User::firstOrCreate(
            ['login' => 'cashier'],
            [
                'full_name' => 'Тестовый кассир',
                'password' => Hash::make('cashier123'),
                'position' => 'cashier',
                'status' => 'active',
                'branch' => 'Коканд МИБ',
                'reward_percentage' => 2.5,
                'added_by' => 'admin',
            ]
        );

        // Назначение роли кассира (если ещё не назначена)
        if (!$cashier->hasRole('cashier')) {
            $cashier->assignRole('cashier');
        }

        $this->command->info('Пользователи успешно созданы/обновлены!');
        $this->command->info('Администратор: login=admin, password=admin123');
        $this->command->info('Кассир: login=cashier, password=cashier123');
    }
}
