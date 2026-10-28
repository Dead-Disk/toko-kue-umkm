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
        if (Schema::hasColumn('order_items', 'catatan')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('order_items', 'catatan')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
