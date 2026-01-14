<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL поддерживает ENUM и MODIFY COLUMN
            DB::statement("ALTER TABLE users MODIFY COLUMN position ENUM('admin', 'cashier', 'manager', 'client_admin') NOT NULL DEFAULT 'cashier'");
        } else {
            // SQLite и другие БД - используем string
            // SQLite не поддерживает изменение типа колонки, поэтому пропускаем
            // В production на MySQL это будет работать корректно
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN position ENUM('admin', 'cashier') NOT NULL DEFAULT 'cashier'");
        }
    }
};
