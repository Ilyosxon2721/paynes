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
            $finePermissions
        );

        // Создание всех прав
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Создание роли администратора и назначение всех прав
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($allPermissions);

        // Создание роли кассира с ограниченными правами
        $cashierRole = Role::create(['name' => 'cashier']);

        // Права кассира для платежей
        $cashierRole->givePermissionTo([
            'payments.create',
            'payments.view',
        ]);

        // Права кассира для обмена валют
        $cashierRole->givePermissionTo([
            'exchanges.create',
            'exchanges.view',
        ]);

        // Права кассира для кредитов
        $cashierRole->givePermissionTo([
            'credits.create',
            'credits.view',
            'credits.repay',
        ]);

        // Права кассира для инкассации
        $cashierRole->givePermissionTo([
            'incashes.create',
            'incashes.view',
        ]);

        // Права кассира для просмотра курсов
        $cashierRole->givePermissionTo([
            'rates.view',
        ]);

        // Права кассира для отчетов (только свои)
        $cashierRole->givePermissionTo([
            'reports.view-own',
        ]);

        $this->command->info('Роли и права успешно созданы!');
        $this->command->info('Роль admin: все права');
        $this->command->info('Роль cashier: ограниченные права');
    }
}
