<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Сброс кэша для прав и ролей
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Создание прав для payments (платежи)
        $paymentPermissions = [
            'payments.create',
            'payments.view',
            'payments.update',
            'payments.delete',
            'payments.confirm',
        ];

        // Создание прав для exchanges (обмен валют)
        $exchangePermissions = [
            'exchanges.create',
            'exchanges.view',
            'exchanges.delete',
        ];

        // Создание прав для credits (кредиты)
        $creditPermissions = [
            'credits.create',
            'credits.view',
            'credits.update',
            'credits.delete',
            'credits.confirm',
            'credits.repay',
        ];

        // Создание прав для incashes (инкассация)
        $incashPermissions = [
            'incashes.create',
            'incashes.view',
            'incashes.delete',
        ];

        // Создание прав для rates (курсы валют)
        $ratePermissions = [
            'rates.create',
            'rates.view',
            'rates.update',
            'rates.delete',
        ];

        // Создание прав для users (пользователи)
        $userPermissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.block',
        ];

        // Создание прав для reports (отчеты)
        $reportPermissions = [
            'reports.view-all',
            'reports.view-own',
            'reports.export',
        ];

        // Создание прав для agents (агенты)
        $agentPermissions = [
            'agents.view',
            'agents.create',
            'agents.update',
            'agents.delete',
        ];

        // Создание прав для fines (штрафы)
        $finePermissions = [
            'fines.view',
            'fines.create',
            'fines.update',
            'fines.delete',
        ];

        // Создание прав для branches (филиалы)
        $branchPermissions = [
            'branches.view',
            'branches.create',
            'branches.update',
            'branches.delete',
        ];

        // Создание прав для merchants (мерчанты)
        $merchantPermissions = [
            'merchants.view',
            'merchants.create',
            'merchants.update',
            'merchants.delete',
        ];

        // Создание прав для commissions (комиссии)
        $commissionPermissions = [
            'commissions.view',
            'commissions.create',
            'commissions.update',
            'commissions.delete',
        ];

        // Создание прав для shifts (кассовые смены)
        $shiftPermissions = [
            'shifts.open',
            'shifts.close',
            'shifts.view',
        ];

        // Объединение всех прав
        $allPermissions = array_merge(
            $paymentPermissions,
            $exchangePermissions,
            $creditPermissions,
            $incashPermissions,
            $ratePermissions,
            $userPermissions,
            $reportPermissions,
            $agentPermissions,
            $finePermissions,
            $branchPermissions,
            $merchantPermissions,
            $commissionPermissions,
            $shiftPermissions
        );

        // Создание всех прав
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ========== РОЛЬ: СУПЕР-АДМИН PAYNES ==========
        // Полный доступ ко всей системе
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($allPermissions);

        // ========== РОЛЬ: УПРАВЛЯЮЩИЙ КОМПАНИИ (CLIENT ADMIN) ==========
        // Полный доступ к своей компании в рамках подписки
        $clientAdminRole = Role::firstOrCreate(['name' => 'client_admin']);
        $clientAdminRole->syncPermissions([
            // Управление филиалами
            'branches.view',
            'branches.create',
            'branches.update',
            'branches.delete',

            // Управление пользователями (менеджеры, кассиры)
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.block',

            // Управление платежами (полный доступ)
            'payments.create',
            'payments.view',
            'payments.update',
            'payments.delete',
            'payments.confirm',

            // Управление обменом валют
            'exchanges.create',
            'exchanges.view',
            'exchanges.delete',

            // Управление кредитами
            'credits.create',
            'credits.view',
            'credits.update',
            'credits.delete',
            'credits.confirm',
            'credits.repay',

            // Управление инкассацией
            'incashes.create',
            'incashes.view',
            'incashes.delete',

            // Управление курсами валют
            'rates.create',
            'rates.view',
            'rates.update',
            'rates.delete',

            // Управление мерчантами
            'merchants.view',
            'merchants.create',
            'merchants.update',
            'merchants.delete',

            // Управление комиссиями
            'commissions.view',
            'commissions.create',
            'commissions.update',
            'commissions.delete',

            // Отчеты (все в рамках компании)
            'reports.view-all',
            'reports.export',

            // Агенты
            'agents.view',
            'agents.create',
            'agents.update',
            'agents.delete',

            // Штрафы
            'fines.view',
            'fines.create',
            'fines.update',
            'fines.delete',

            // Просмотр смен
            'shifts.view',
        ]);

        // ========== РОЛЬ: МЕНЕДЖЕР ФИЛИАЛА ==========
        // Управление назначенными филиалами
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions([
            // Просмотр филиалов (только свои)
            'branches.view',

            // Управление кассирами своих филиалов
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.block',

            // Управление платежами (подтверждение/удаление)
            'payments.create',
            'payments.view',
            'payments.update',
            'payments.delete',
            'payments.confirm',

            // Управление обменом валют
            'exchanges.create',
            'exchanges.view',
            'exchanges.delete',

            // Управление кредитами
            'credits.create',
            'credits.view',
            'credits.update',
            'credits.delete',
            'credits.confirm',
            'credits.repay',

            // Управление инкассацией
            'incashes.create',
            'incashes.view',
            'incashes.delete',

            // Управление курсами валют своих филиалов
            'rates.create',
            'rates.view',
            'rates.update',
            'rates.delete',

            // Отчеты по своим филиалам
            'reports.view-all',
            'reports.export',

            // Штрафы
            'fines.view',
            'fines.create',
            'fines.update',
            'fines.delete',

            // Просмотр смен
            'shifts.view',
        ]);

        // ========== РОЛЬ: КАССИР ==========
        // Работа с клиентами и операции
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $cashierRole->syncPermissions([
            // Платежи
            'payments.create',
            'payments.view',
            // Обмен валют
            'exchanges.create',
            'exchanges.view',
            // Кредиты
            'credits.create',
            'credits.view',
            'credits.repay',
            // Инкассация
            'incashes.create',
            'incashes.view',
            // Курсы валют
            'rates.view',
            // Отчеты (только свои)
            'reports.view-own',
            // Смены
            'shifts.open',
            'shifts.close',
            'shifts.view',
        ]);

        $this->command->info('✅ Роли и права успешно созданы!');
        $this->command->info('📋 Роль admin: все права (супер-админ Paynes)');
        $this->command->info('🏢 Роль client_admin: полный доступ к компании');
        $this->command->info('👔 Роль manager: управление филиалами и кассирами');
        $this->command->info('💰 Роль cashier: работа с клиентами и операции');
    }
}
