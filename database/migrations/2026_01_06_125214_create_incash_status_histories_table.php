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
        Schema::create('incash_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incash_id')->constrained('incashes')->cascadeOnDelete();
            $table->enum('old_status', ['pending', 'confirmed', 'deleted'])->nullable();
            $table->enum('new_status', ['pending', 'confirmed', 'deleted']);
            $table->foreignId('changed_by')->constrained('users');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('incash_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incash_status_histories');
    }
};
