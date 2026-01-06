<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rate;

class ExchangeRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание текущего курса USD
        Rate::create([
            'currency_from' => 'USD',
            'currency_to' => 'UZS',
            'buy_rate' => 12650.00,  // Курс покупки USD
            'sell_rate' => 12700.00, // Курс продажи USD
            'is_active' => true,
        ]);

        $this->command->info('Курсы валют успешно созданы!');
        $this->command->info('USD → UZS: Покупка 12,650 / Продажа 12,700');
    }
}
