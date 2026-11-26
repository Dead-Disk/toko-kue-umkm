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
        Schema::create('chat_threads', function (Blueprint $table): void {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone', 30)->index();
            $table->string('thread_type', 20)->default('inquiry')->index(); // 'order' atau 'inquiry'
            $table->string('order_code', 50)->nullable()->index();
            $table->unsignedInteger('unread_admin_count')->default(0);
            $table->unsignedInteger('unread_customer_count')->default(0);
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_threads');
    }
};
