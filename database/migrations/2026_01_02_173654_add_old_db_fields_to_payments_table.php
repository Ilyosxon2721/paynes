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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('city')->nullable()->after('status');
            $table->string('region')->nullable()->after('city');
            $table->decimal('cash_back', 10, 2)->nullable()->after('total');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete()->after('cashier_id');
            $table->string('payment_system')->nullable()->after('payment_method'); // UzCard, Humo, etc
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn(['city', 'region', 'cash_back', 'agent_id', 'payment_system']);
        });
    }
};
