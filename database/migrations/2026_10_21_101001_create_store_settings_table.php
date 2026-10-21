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
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->nullable();
            $table->text('store_description')->nullable();
            $table->string('store_address')->nullable();
            $table->string('store_phone', 30)->nullable();

            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_holder', 100)->nullable();
            $table->string('qris_image_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
