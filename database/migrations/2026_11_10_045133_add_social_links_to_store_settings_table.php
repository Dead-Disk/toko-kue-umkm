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
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('store_email')->nullable()->after('store_phone');
            $table->string('instagram_url')->nullable()->after('store_email');
            $table->string('facebook_url')->nullable()->after('instagram_url');
            $table->string('tiktok_url')->nullable()->after('facebook_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['store_email', 'instagram_url', 'facebook_url', 'tiktok_url']);
        });
    }
};
