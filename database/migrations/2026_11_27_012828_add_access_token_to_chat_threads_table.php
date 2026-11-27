<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_threads', function (Blueprint $table): void {
            $table->string('access_token', 64)->nullable()->unique()->after('order_code');
        });

        // Backfill hash access_token untuk thread yang sudah ada sebelumnya
        DB::table('chat_threads')
            ->whereNull('access_token')
            ->orderBy('id')
            ->chunkById(100, function ($threads): void {
                foreach ($threads as $thread) {
                    do {
                        $plainToken = Str::random(48);
                        $hashedToken = hash('sha256', $plainToken);
                    } while (DB::table('chat_threads')->where('access_token', $hashedToken)->exists());

                    DB::table('chat_threads')
                        ->where('id', $thread->id)
                        ->update(['access_token' => $hashedToken]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_threads', function (Blueprint $table): void {
            $table->dropColumn('access_token');
        });
    }
};
