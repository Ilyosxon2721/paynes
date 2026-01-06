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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->constrained('users');
            $table->date('shift_date');
            $table->time('opened_at');
            $table->time('closed_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');

            // Начальные балансы при открытии смены
            $table->decimal('opening_cash_uzs', 15, 2)->default(0);
            $table->decimal('opening_cashless_uzs', 15, 2)->default(0);
            $table->decimal('opening_card_uzs', 15, 2)->default(0);
            $table->decimal('opening_p2p_uzs', 15, 2)->default(0);
            $table->decimal('opening_cash_usd', 15, 2)->default(0);

            // Итоговые балансы при закрытии смены
            $table->decimal('closing_cash_uzs', 15, 2)->nullable();
            $table->decimal('closing_cashless_uzs', 15, 2)->nullable();
            $table->decimal('closing_card_uzs', 15, 2)->nullable();
            $table->decimal('closing_p2p_uzs', 15, 2)->nullable();
            $table->decimal('closing_cash_usd', 15, 2)->nullable();

            // Примечания
            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();

            $table->timestamps();

            $table->index(['cashier_id', 'shift_date']);
            $table->unique(['cashier_id', 'shift_date', 'opened_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
