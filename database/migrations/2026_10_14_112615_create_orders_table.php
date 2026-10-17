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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique();
            $table->enum('tipe_pesanan', ['reguler', 'custom']);
            $table->string('nama_pemesan');
            $table->string('nomor_wa');
            $table->text('alamat_lengkap');
            $table->enum('opsi_pengiriman', ['kurir_toko', 'ambil_sendiri']);
            $table->enum('metode_pembayaran', ['transfer_bank', 'qris']);
            $table->unsignedBigInteger('total_harga')->default(0);
            $table->enum('status', ['pending', 'diproses', 'selesai', 'batal'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
