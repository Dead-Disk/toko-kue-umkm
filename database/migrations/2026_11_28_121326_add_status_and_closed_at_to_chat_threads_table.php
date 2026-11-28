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
        Schema::table('chat_threads', function (Blueprint $table): void {
            $table->string('status', 20)->default('active')->index()->after('order_code');
            $table->timestamp('closed_at')->nullable()->after('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_threads', function (Blueprint $table): void {
            $table->dropColumn(['status', 'closed_at']);
        });
    }
};
