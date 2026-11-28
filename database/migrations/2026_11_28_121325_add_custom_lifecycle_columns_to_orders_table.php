<?php

use App\Models\ChatThread;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('nomor_wa_normalized', 30)->nullable()->index()->after('nomor_wa');
            $table->enum('status', ['pending', 'proses', 'selesai', 'ditolak', 'batal', 'expired'])
                ->default('pending')
                ->change();
            $table->timestamp('expires_at')->nullable()->index()->after('tanggal_ambil');
            $table->timestamp('acc_at')->nullable()->after('expires_at');
            $table->timestamp('cancelled_at')->nullable()->after('acc_at');
            $table->timestamp('expired_at')->nullable()->after('cancelled_at');
            $table->string('cancel_reason', 255)->nullable()->after('expired_at');
        });

        DB::table('orders')
            ->select(['id', 'nomor_wa'])
            ->orderBy('id')
            ->chunkById(100, function ($orders): void {
                foreach ($orders as $order) {
                    $normalized = ChatThread::normalizePhone($order->nomor_wa);
                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update(['nomor_wa_normalized' => $normalized !== '' ? $normalized : null]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')->where('status', 'expired')->update(['status' => 'batal']);

        Schema::table('orders', function (Blueprint $table): void {
            $table->enum('status', ['pending', 'proses', 'selesai', 'ditolak', 'batal'])
                ->default('pending')
                ->change();
            $table->dropColumn([
                'nomor_wa_normalized',
                'expires_at',
                'acc_at',
                'cancelled_at',
                'expired_at',
                'cancel_reason',
            ]);
        });
    }
};
