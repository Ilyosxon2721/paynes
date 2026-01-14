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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->comment('Название комиссии');
            $table->enum('type', ['fixed', 'percentage', 'combined'])->default('percentage')
                ->comment('Тип комиссии: фиксированная, процент, или комбинированная');
            $table->decimal('fixed_amount', 12, 2)->nullable()->comment('Фиксированная сумма комиссии');
            $table->decimal('percentage', 5, 2)->nullable()->comment('Процент комиссии');
            $table->decimal('min_amount', 12, 2)->nullable()->comment('Минимальная сумма комиссии');
            $table->decimal('max_amount', 12, 2)->nullable()->comment('Максимальная сумма комиссии');
            $table->enum('applies_to', ['all', 'payment_type', 'merchant'])->default('all')
                ->comment('К чему применяется: всем операциям, типу платежа, или мерчанту');
            $table->foreignId('merchant_id')->nullable()->constrained()->nullOnDelete()
                ->comment('Если применяется к конкретному мерчанту');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('valid_from')->nullable()->comment('Действует с');
            $table->date('valid_until')->nullable()->comment('Действует до');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Индексы для быстрого поиска
            $table->index(['client_id', 'status']);
            $table->index(['payment_type_id', 'status']);
            $table->index(['merchant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
