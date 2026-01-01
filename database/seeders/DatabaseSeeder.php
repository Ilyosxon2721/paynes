<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Сначала создаем роли и права
            RolesAndPermissionsSeeder::class,

            // Затем создаем типы платежей
            PaymentTypesSeeder::class,

            // Затем создаем пользователей (они зависят от ролей)
            UsersSeeder::class,

            // И в конце курсы валют
            RatesSeeder::class,
        ]);
    }
}
