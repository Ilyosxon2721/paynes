<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTypes = [
            [
                'name' => 'Коммунальные услуги',
                'organization' => 'АО "Узбекэнерго"',
                'bank' => 'Национальный банк Узбекистана',
                'account_number' => '20208000705210001001',
                'mfo' => '00014',
                'inn' => '200574890',
                'type' => 'utility',
                'commission_percentage' => 2.00,
                'commission_fixed' => 0,
            ],
            [
                'name' => 'Государственные налоги',
                'organization' => 'Государственный налоговый комитет РУз',
                'bank' => 'Национальный банк Узбекистана',
                'account_number' => '20208000805210001001',
                'mfo' => '00014',
                'inn' => '200123456',
                'treasury_account' => '40101810100000010001',
                'type' => 'tax',
                'commission_percentage' => 1.50,
                'commission_fixed' => 5000.00,
            ],
            [
                'name' => 'Штрафы ГИБДД',
                'organization' => 'ГУБДД МВД РУз',
                'bank' => 'Национальный банк Узбекистана',
                'account_number' => '20208000905210001001',
                'mfo' => '00014',
                'inn' => '200987654',
                'treasury_account' => '40101810100000010002',
                'type' => 'fine',
                'commission_percentage' => 3.00,
                'commission_fixed' => 0,
            ],
            [
                'name' => 'Оплата мобильной связи',
                'organization' => 'ООО "Ucell"',
                'bank' => 'АКБ "Ипотека Банк"',
                'account_number' => '20208000705210002001',
                'mfo' => '00433',
                'inn' => '201234567',
                
                'type' => 'mobile',
                'commission_percentage' => 1.00,
                'commission_fixed' => 0,
            ],
            [
                'name' => 'Интернет',
                'organization' => 'АО "Узбектелеком"',
                'bank' => 'АКБ "Асака"',
                'account_number' => '20208000805210002001',
                'mfo' => '00007',
                'inn' => '202345678',
                
                'type' => 'internet',
                'commission_percentage' => 0,
                'commission_fixed' => 1000.00,
            ],
            [
                'name' => 'Телевидение',
                'organization' => 'ООО "ZED TV"',
                'bank' => 'АКБ "Ипотека Банк"',
                'account_number' => '20208000905210002001',
                'mfo' => '00433',
                'inn' => '203456789',
                
                'type' => 'tv',
                'commission_percentage' => 1.50,
                'commission_fixed' => 500.00,
            ],
            [
                'name' => 'Образовательные услуги',
                'organization' => 'Министерство высшего и среднего специального образования РУз',
                'bank' => 'Национальный банк Узбекистана',
                'account_number' => '20208001005210001001',
                'mfo' => '00014',
                'inn' => '204567890',
                'treasury_account' => '40101810100000010003',
                'type' => 'education',
                'commission_percentage' => 2.50,
                'commission_fixed' => 0,
            ],
        ];

        foreach ($paymentTypes as $type) {
            PaymentType::create($type);
        }

        $this->command->info('Типы платежей успешно созданы!');
        $this->command->info('Создано ' . count($paymentTypes) . ' типов платежей.');
    }
}
