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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('Название мерчанта');
            $table->string('code')->unique()->comment('Уникальный код мерчанта');
            $table->string('api_key')->nullable()->comment('API ключ для интеграции');
            $table->string('api_secret')->nullable()->comment('API секрет');
            $table->string('callback_url')->nullable()->comment('URL для webhook уведомлений');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('allowed_ips')->nullable()->comment('Разрешенные IP адреса (JSON)');
            $table->text('settings')->nullable()->comment('Дополнительные настройки (JSON)');
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
        Schema::dropIfExists('merchants');
    }
};
