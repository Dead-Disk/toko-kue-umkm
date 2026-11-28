<?php

namespace App\Http\Middleware;

use App\Models\ChatThread;
use App\Models\Order;
use App\Services\CustomOrderChatService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerChatAccess
{
    public function __construct(public CustomOrderChatService $chatService) {}

    /**
     * Lindungi rute dan endpoint ruang chat pembeli agar tidak dapat diakses
     * tanpa session verifikasi yang valid.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Akses URL langsung ruang chat pesanan (/customer/chat/pesanan/{kode_pesanan})
        $routeOrderCode = $request->route('kode_pesanan');
        if (is_string($routeOrderCode) && $routeOrderCode !== '') {
            $trimmedCode = trim($routeOrderCode);
            $order = Order::where('kode_pesanan', $trimmedCode)->first()
                ?? Order::whereRaw('LOWER(kode_pesanan) = ?', [strtolower($trimmedCode)])->first();

            if (! $order || ! $this->chatService->hasVerifiedOrderAccess($order->kode_pesanan)) {
                $targetCode = $order?->kode_pesanan ?? strtoupper($trimmedCode);
                session(['customer_chat_intended_order' => $targetCode]);

                return redirect()
                    ->route('customer.chat.index', [
                        'mode' => 'order',
                        'order' => $targetCode,
                    ])
                    ->withErrors([
                        'verification' => 'Akses ditahan. Silakan verifikasi Kode Pesanan, Nomor WhatsApp, dan PIN untuk membuka ruang chat pesanan ini.',
                    ]);
            }

            // Session sudah valid — pastikan thread ada (idempoten), lalu lanjutkan.
            // Tidak perlu memanggil grantOrderChatSession() lagi; session telah terverifikasi
            // sebelumnya melalui verifyOrder() dan tidak boleh di-refresh tanpa PIN ulang.
            ChatThread::firstOrCreate(
                ['order_code' => $order->kode_pesanan],
                [
                    'customer_name' => $order->nama_pemesan,
                    'customer_phone' => ChatThread::normalizePhone($order->nomor_wa),
                    'thread_type' => 'order',
                    'unread_admin_count' => 0,
                    'unread_customer_count' => 0,
                    'last_message_at' => now(),
                ]
            );

            return $next($request);
        }

        // 2. Endpoint chat aktif (send, poll, add item, attachment)
        $sessionThreadId = session('customer_active_thread_id');
        if (! $sessionThreadId) {
            return $this->denyAccess($request);
        }

        $thread = ChatThread::find($sessionThreadId);
        if (! $thread) {
            $this->chatService->forgetCustomerChatSession();

            return $this->denyAccess($request);
        }

        if ($thread->thread_type === 'order' && filled($thread->order_code)) {
            if (! $this->chatService->hasVerifiedOrderAccess($thread->order_code)) {
                $orderCode = $thread->order_code;
                $this->chatService->forgetCustomerChatSession();

                return $this->denyAccess($request, $orderCode);
            }
        }

        return $next($request);
    }

    private function denyAccess(Request $request, ?string $orderCode = null): Response
    {
        if ($request->expectsJson() || $request->is('customer/chat/send') || $request->is('customer/chat/poll') || $request->is('customer/chat/items')) {
            return response()->json([
                'error' => 'Sesi chat belum terverifikasi atau telah berakhir.',
                'redirect_url' => route('customer.chat.index', array_filter([
                    'mode' => $orderCode ? 'order' : null,
                    'order' => $orderCode,
                ])),
            ], 403);
        }

        return redirect()
            ->route('customer.chat.index', array_filter([
                'mode' => $orderCode ? 'order' : null,
                'order' => $orderCode,
            ]))
            ->withErrors([
                'verification' => 'Sesi chat belum terverifikasi atau telah berakhir. Silakan verifikasi ulang.',
            ]);
    }
}
