<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan metode pembayaran tunai / bayar di toko (COD).
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->enum('metode_pembayaran', ['transfer_bank', 'qris', 'cash'])->change();
        });
    }

    /**
     * Rollback: pesanan tunai diubah ke transfer bank karena 'cash' tidak ada di enum lama.
     */
    public function down(): void
    {
        DB::table('orders')->where('metode_pembayaran', 'cash')->update(['metode_pembayaran' => 'transfer_bank']);

        Schema::table('orders', function (Blueprint $table): void {
            $table->enum('metode_pembayaran', ['transfer_bank', 'qris'])->change();
        });
    }
};
