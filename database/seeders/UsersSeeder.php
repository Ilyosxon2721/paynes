<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание супер администратора
        $admin = User::create([
            'login' => 'admin',
            'full_name' => 'Администратор системы',
            'password' => Hash::make('admin123'),
            'position' => 'admin',
            'status' => 'active',
            'branch' => 'Главный офис',
            'salary_percentage' => 0,
        ]);

        // Назначение роли администратора
        $admin->assignRole('admin');

        // Создание кассира 1
        $cashier1 = User::create([
            'login' => 'cashier1',
            'full_name' => 'Кассир Иванов Иван Иванович',
            'password' => Hash::make('cashier123'),
            'position' => 'cashier',
            'status' => 'active',
            'branch' => 'Филиал №1',
            'salary_percentage' => 10.00,
        ]);

        // Назначение роли кассира
        $cashier1->assignRole('cashier');

        // Создание кассира 2
        $cashier2 = User::create([
            'login' => 'cashier2',
            'full_name' => 'Кассир Петрова Мария Сергеевна',
            'password' => Hash::make('cashier123'),
            'position' => 'cashier',
            'status' => 'active',
            'branch' => 'Филиал №2',
            'salary_percentage' => 8.00,
        ]);

        // Назначение роли кассира
        $cashier2->assignRole('cashier');

        $this->command->info('Пользователи успешно созданы!');
        $this->command->info('');
        $this->command->info('Администратор:');
        $this->command->info('  Логин: admin');
        $this->command->info('  Пароль: admin123');
        $this->command->info('');
        $this->command->info('Кассир 1:');
        $this->command->info('  Логин: cashier1');
        $this->command->info('  Пароль: cashier123');
        $this->command->info('  Процент от зарплаты: 10%');
        $this->command->info('');
        $this->command->info('Кассир 2:');
        $this->command->info('  Логин: cashier2');
        $this->command->info('  Пароль: cashier123');
        $this->command->info('  Процент от зарплаты: 8%');
    }
}
