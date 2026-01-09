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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name')->comment('Название плана подписки');
            $table->decimal('monthly_price', 12, 2)->comment('Ежемесячная цена');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('next_billing_date')->nullable();
            $table->string('status')->default('active')->comment('active, expired, cancelled, suspended');
            $table->integer('max_users')->default(5)->comment('Максимальное количество пользователей');
            $table->integer('max_branches')->default(1)->comment('Максимальное количество филиалов');
            $table->boolean('auto_renew')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
