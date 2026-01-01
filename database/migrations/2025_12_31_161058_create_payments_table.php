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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('list_number')->nullable();
            $table->string('random_number')->nullable();
            $table->date('date');
            $table->time('time');
            $table->foreignId('payment_type_id')->constrained('payment_types')->cascadeOnDelete();
            $table->string('payer_name');
            $table->text('purpose')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->enum('payment_method', ['cash', 'card'])->default('cash');
            $table->enum('currency', ['UZS', 'USD'])->default('UZS');
            $table->enum('status', ['pending', 'confirmed', 'deleted', 'processed'])->default('pending');
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
