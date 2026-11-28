<?php

namespace App\Console\Commands;

use App\Services\CustomOrderChatService;
use Illuminate\Console\Command;

class ExpireStaleCustomOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom-orders:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kedaluwarsakan pesanan custom cake yang menggantung melewati batas waktu diskusi dan buka kembali kunci nomor HP.';

    /**
     * Execute the console command.
     */
    public function handle(CustomOrderChatService $chatService): int
    {
        $count = $chatService->expireStaleCustomOrders();

        $this->info("Pesanan custom yang dikedaluwarsakan: {$count}");

        return self::SUCCESS;
    }
}
