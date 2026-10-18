<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pindahkan status 'diproses' menjadi 'proses' dan tambahkan 'ditolak'.
     * Nilai lama dipertahankan saat alter pertama agar baris 'diproses' tidak terpotong di MySQL.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->enum('status', ['pending', 'diproses', 'proses', 'selesai', 'ditolak', 'batal'])
                ->default('pending')
                ->change();
        });

        DB::table('orders')->where('status', 'diproses')->update(['status' => 'proses']);

        Schema::table('orders', function (Blueprint $table): void {
            $table->enum('status', ['pending', 'proses', 'selesai', 'ditolak', 'batal'])
                ->default('pending')
                ->change();
        });
    }

    /**
     * Rollback: 'proses' kembali ke 'diproses', 'ditolak' jatuh ke 'batal' (kehilangan pembeda alasan).
     */
    public function down(): void
    {
        DB::table('orders')->where('status', 'proses')->update(['status' => 'diproses']);
        DB::table('orders')->where('status', 'ditolak')->update(['status' => 'batal']);

        Schema::table('orders', function (Blueprint $table): void {
            $table->enum('status', ['pending', 'diproses', 'selesai', 'batal'])
                ->default('pending')
                ->change();
        });
    }
};
