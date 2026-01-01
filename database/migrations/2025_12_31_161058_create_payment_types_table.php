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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('bank')->nullable();
            $table->string('account_number')->nullable();
            $table->string('mfo')->nullable();
            $table->string('inn')->nullable();
            $table->string('treasury_account')->nullable();
            $table->string('type')->nullable();
            $table->decimal('commission_percentage', 5, 2)->default(0)->comment('Commission percentage');
            $table->decimal('commission_fixed', 15, 2)->default(0)->comment('Fixed commission amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
