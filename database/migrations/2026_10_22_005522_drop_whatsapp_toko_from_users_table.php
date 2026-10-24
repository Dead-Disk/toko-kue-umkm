<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kontak toko dipusatkan di store_settings, bukan di tabel users.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'whatsapp_toko')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whatsapp_toko');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'whatsapp_toko')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_toko')->nullable()->after('password');
        });
    }
};
