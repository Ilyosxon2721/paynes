<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rate;
use Carbon\Carbon;

class RatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем сегодняшнюю дату
        $today = Carbon::today();

        // Создание курса валют за 3 дня назад
        Rate::create([
            'buy_rate' => 12450.00,
            'sell_rate' => 12550.00,
            'date' => $today->copy()->subDays(2),
        ]);

        // Создание курса валют за 2 дня назад
        Rate::create([
            'buy_rate' => 12475.00,
            'sell_rate' => 12575.00,
            'date' => $today->copy()->subDays(1),
        ]);

        // Создание курса валют за вчера
        Rate::create([
            'buy_rate' => 12500.00,
            'sell_rate' => 12600.00,
            'date' => $today,
        ]);

        $this->command->info('Курсы валют успешно созданы!');
        $this->command->info('');
        $this->command->info('Курсы USD за последние 3 дня:');
        $this->command->info('  ' . $today->copy()->subDays(2)->format('d.m.Y') . ' - Покупка: 12450, Продажа: 12550');
        $this->command->info('  ' . $today->copy()->subDays(1)->format('d.m.Y') . ' - Покупка: 12475, Продажа: 12575');
        $this->command->info('  ' . $today->format('d.m.Y') . ' - Покупка: 12500, Продажа: 12600');
    }
}
