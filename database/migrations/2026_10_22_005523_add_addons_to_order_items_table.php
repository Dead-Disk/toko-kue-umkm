<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pastikan kolom addons tersedia di semua environment. Kolom ini sudah
     * ada di database lama sehingga penambahan perlu dijaga agar tidak dobel.
     */
    public function up(): void
    {
        if (Schema::hasColumn('order_items', 'addons')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('addons')->nullable()->after('ukuran_kue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('order_items', 'addons')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('addons');
        });
    }
};
