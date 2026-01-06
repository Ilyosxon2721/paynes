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
            // 1. Сначала создаем роли и права
            RolesAndPermissionsSeeder::class,

            // 2. Затем создаем пользователей (они зависят от ролей)
            AdminUserSeeder::class,

            // 3. Затем создаем типы платежей
            PaymentTypesSeeder::class,

            // 4. И в конце курсы валют
            ExchangeRatesSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Все базовые данные успешно созданы!');
        $this->command->newLine();
        $this->command->info('🔐 Для входа используйте:');
        $this->command->info('   Администратор: login=admin, password=admin123');
        $this->command->info('   Кассир: login=cashier, password=cashier123');
        $this->command->newLine();
    }
}
