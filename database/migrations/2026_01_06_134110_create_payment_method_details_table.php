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
        Schema::create('payment_method_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->enum('method', ['cash', 'card', 'transfer', 'p2p']);
            $table->decimal('amount', 15, 2);
            $table->string('payment_system')->nullable(); // для card: uzcard, humo, visa, mastercard
            $table->string('reference')->nullable(); // номер транзакции/карты/счета
            $table->timestamps();

            $table->index('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_details');
    }
};
