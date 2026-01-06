<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->foreignId('cashier_shift_id')->nullable()->after('cashier_id')->constrained('cashier_shifts');
            $table->decimal('remaining_balance', 15, 2)->default(0)->after('status');
            $table->decimal('total_repaid', 15, 2)->default(0)->after('remaining_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropForeign(['cashier_shift_id']);
            $table->dropColumn(['cashier_shift_id', 'remaining_balance', 'total_repaid']);
        });
    }
};
