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
            $table->string('banner_path')->nullable()->after('logo_path');
            $table->string('banner_scale', 20)->default('cover')->after('qris_image_path');
            $table->string('banner_size', 20)->default('21:9')->after('banner_scale');
            $table->unsignedSmallInteger('banner_slide_duration')->default(5)->after('banner_size');
            $table->boolean('banner_auto_slide')->default(true)->after('banner_slide_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'banner_path',
                'banner_scale',
                'banner_size',
                'banner_slide_duration',
                'banner_auto_slide',
            ]);
        });
    }
};
