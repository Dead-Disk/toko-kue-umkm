<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerChatController extends Controller
{
    /**
     * Tampilkan halaman chat pembeli.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // 1. Jika pembeli membuka URL dengan ?token=..., verifikasi hash token rahasia
        $token = $request->query('token');
        if (! empty($token)) {
            $tokenThread = ChatThread::findByPlainToken((string) $token);

            if ($tokenThread) {
                $this->markSellerMessagesAsRead($tokenThread);

                // Simpan token thread & plain token ke sesi aktif pembeli
                session([
                    'customer_active_thread_id' => $tokenThread->id,
                    'customer_plain_access_token' => $tokenThread->plainAccessToken,
                    'customer_verified_phone' => $tokenThread->customer_phone,
                    'customer_verified_order' => $tokenThread->order_code,
                    'customer_chat_phone' => $tokenThread->customer_phone,
                    'customer_chat_name' => $tokenThread->customer_name,
                    'customer_chat_order' => $tokenThread->order_code,
                ]);

                return view('customer.chat.index', [
                    'thread' => $tokenThread,
                    'presetPhone' => $tokenThread->customer_phone,
                    'presetName' => $tokenThread->customer_name,
                    'presetOrder' => $tokenThread->order_code,
                    'defaultMode' => $tokenThread->thread_type === 'order' ? 'order' : 'inquiry',
                ]);
            }
        }

        // 2. Jika pembeli meminta membuka alur verifikasi (misalnya mode=order atau verify=1/verify=3 dari form Custom Cake),
        // paksa reset sesi thread aktif agar wajib verifikasi Nomor HP + Kode Pesanan terlebih dahulu.
        $forceVerify = $request->filled('verify') || ($request->query('mode') === 'order' && ! $request->has('order'));

        if ($forceVerify) {
            session()->forget([
                'customer_active_thread_id',
                'customer_plain_access_token',
                'customer_verified_phone',
                'customer_verified_order',
                'customer_chat_order',
            ]);
        }

        $sessionThreadId = session('customer_active_thread_id');
        $thread = null;

        if ($sessionThreadId && ! $forceVerify) {
            $thread = ChatThread::with([
                'messages' => fn ($query) => $query->orderBy('id'),
            ])->find($sessionThreadId);

            if ($thread) {
                $this->markSellerMessagesAsRead($thread);
                $thread->plainAccessToken = $this->resolveSessionPlainToken($thread);
            }
        }

        $presetPhone = $request->query('phone') ?? session('customer_chat_phone');
        $presetName = $request->query('name') ?? session('customer_chat_name');
        $presetOrder = $request->query('order') ?? session('customer_chat_order');
        $defaultMode = $request->query('mode', ! empty($presetOrder) ? 'order' : 'inquiry');

        return view('customer.chat.index', [
            'thread' => $thread,
            'presetPhone' => $presetPhone,
            'presetName' => $presetName,
            'presetOrder' => $presetOrder,
            'defaultMode' => $defaultMode,
        ]);
    }

    /**
     * Mode 1: Verifikasi 2 Langkah (Nomor HP + Kode Pesanan) untuk membuka chat pesanan.
     */
    public function verifyOrder(Request $request): JsonResponse|RedirectResponse
    {
        // Rate limiting untuk mencegah brute force percobaan kombinasi kode pesanan & nomor WA
        $ip = $request->ip();
        $rateKey = 'chat-verify:'.$ip;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            $seconds = RateLimiter::availableIn($rateKey);
            $msg = "Terlalu banyak percobaan verifikasi. Silakan coba lagi dalam {$seconds} detik.";

            return $this->verificationFailed($request, $msg);
        }

        RateLimiter::hit($rateKey, 60);

        $validated = $request->validate([
            'customer_phone' => ['required', 'string', 'max:30'],
            'order_code' => ['required', 'string', 'max:50'],
            'chat_pin' => ['required', 'digits:6'],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [
            'chat_pin.required' => 'PIN rahasia pesanan wajib diisi.',
            'chat_pin.digits' => 'PIN rahasia pesanan harus tepat 6 digit angka.',
        ]);

        $normalizedPhone = ChatThread::normalizePhone($validated['customer_phone']);
        $orderCode = trim($validated['order_code']);

        // 1. Verifikasi kecocokan nomor HP, Kode Pesanan, dan PIN rahasia terhadap tabel orders
        $order = Order::where('kode_pesanan', $orderCode)->first();

        if (! $order) {
            return $this->verificationFailed($request, 'Kombinasi Nomor WhatsApp, Kode Pesanan, dan PIN tidak cocok.');
        }

        $orderPhoneNormalized = ChatThread::normalizePhone($order->nomor_wa);

        if ($orderPhoneNormalized !== $normalizedPhone || ! $order->verifyChatPin($validated['chat_pin'])) {
            return $this->verificationFailed($request, 'Kombinasi Nomor WhatsApp, Kode Pesanan, dan PIN tidak cocok.');
        }

        RateLimiter::clear($rateKey);

        // 2. Cari atau buat thread chat untuk pesanan ini
        $thread = ChatThread::where('order_code', $order->kode_pesanan)->first();

        if (! $thread) {
            $thread = ChatThread::create([
                'customer_name' => $order->nama_pemesan,
                'customer_phone' => $normalizedPhone,
                'thread_type' => 'order',
                'order_code' => $order->kode_pesanan,
                'unread_admin_count' => 0,
                'unread_customer_count' => 0,
                'last_message_at' => now(),
            ]);
        } else {
            // Pastikan data thread sinkron dan terbitkan token rahasia baru saat verifikasi ulang
            $thread->update([
                'customer_name' => $order->nama_pemesan,
                'customer_phone' => $normalizedPhone,
                'thread_type' => 'order',
            ]);
            $thread->rotateAccessToken();
        }

        // 3. Simpan token verifikasi & plain token ke sesi browser
        session([
            'customer_active_thread_id' => $thread->id,
            'customer_plain_access_token' => $thread->plainAccessToken,
            'customer_verified_phone' => $normalizedPhone,
            'customer_verified_order' => $order->kode_pesanan,
            'customer_chat_phone' => $normalizedPhone,
            'customer_chat_name' => $order->nama_pemesan,
            'customer_chat_order' => $order->kode_pesanan,
        ]);

        // Jika ada pesan awal
        if (! blank($validated['message'] ?? null)) {
            ChatMessage::create([
                'thread_id' => $thread->id,
                'sender_type' => 'customer',
                'sender_id' => null,
                'sender_name' => $order->nama_pemesan,
                'message' => $validated['message'],
                'is_read' => false,
            ]);

            $thread->increment('unread_admin_count');
            $thread->update(['last_message_at' => now()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'thread_id' => $thread->id,
                'token' => $thread->plainAccessToken,
                'secret_url' => $thread->getCustomerSecretUrl(),
            ]);
        }

        return redirect()->route('customer.chat.index');
    }

    /**
     * Mode 2: Chat Baru Pra-Order / Tanya-tanya (Tanpa Kode Pesanan).
     * Aturan keamanan: membuat thread obrolan baru terisolasi ('inquiry'),
     * setiap kali obrolan baru dibuat memiliki thread terpisah dengan secret access_token.
     */
    public function startInquiry(Request $request): JsonResponse|RedirectResponse
    {
        // Rate limiting anti-spam pembuatan thread percakapan baru
        $ip = $request->ip();
        $rateKey = 'chat-inquiry:'.$ip;
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            $errorMsg = "Terlalu banyak permintaan chat baru. Silakan tunggu {$seconds} detik sebelum membuat obrolan baru.";
            if ($request->wantsJson()) {
                return response()->json(['error' => $errorMsg], 429);
            }

            return back()->withErrors(['inquiry' => $errorMsg]);
        }

        RateLimiter::hit($rateKey, 60);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $normalizedPhone = ChatThread::normalizePhone($validated['customer_phone']);

        if (empty($normalizedPhone)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Nomor HP/WhatsApp tidak valid.'], 422);
            }

            return back()->withErrors(['customer_phone' => 'Nomor HP/WhatsApp tidak valid.']);
        }

        // Buat thread terisolasi khusus inquiry pra-order baru
        $thread = ChatThread::create([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $normalizedPhone,
            'thread_type' => 'inquiry',
            'order_code' => null,
            'unread_admin_count' => 0,
            'unread_customer_count' => 0,
            'last_message_at' => now(),
        ]);

        // Simpan sesi aktif beserta plain token sementara untuk browser pembeli
        session([
            'customer_active_thread_id' => $thread->id,
            'customer_plain_access_token' => $thread->plainAccessToken,
            'customer_verified_phone' => $normalizedPhone,
            'customer_verified_order' => null,
            'customer_chat_phone' => $normalizedPhone,
            'customer_chat_name' => $thread->customer_name,
            'customer_chat_order' => null,
        ]);

        if (! blank($validated['message'] ?? null)) {
            ChatMessage::create([
                'thread_id' => $thread->id,
                'sender_type' => 'customer',
                'sender_id' => null,
                'sender_name' => $thread->customer_name,
                'message' => $validated['message'],
                'is_read' => false,
            ]);

            $thread->increment('unread_admin_count');
            $thread->update(['last_message_at' => now()]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'thread_id' => $thread->id,
                'token' => $thread->plainAccessToken,
                'secret_url' => $thread->getCustomerSecretUrl(),
            ]);
        }

        return redirect()->route('customer.chat.index');
    }

    /**
     * Kirim pesan dalam sesi thread yang sedang terverifikasi.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $sessionThreadId = session('customer_active_thread_id');
        if (! $sessionThreadId) {
            return response()->json(['error' => 'Sesi chat belum terverifikasi atau telah berakhir.'], 403);
        }

        $thread = ChatThread::find($sessionThreadId);
        if (! $thread) {
            return response()->json(['error' => 'Thread chat tidak ditemukan.'], 404);
        }

        // Rate limiting kirim pesan untuk mencegah spam flood
        $ip = $request->ip();
        $rateKey = 'chat-send:'.$thread->id.':'.$ip;
        if (RateLimiter::tooManyAttempts($rateKey, 20)) {
            return response()->json(['error' => 'Terlalu cepat mengirim pesan. Silakan tunggu beberapa detik.'], 429);
        }
        RateLimiter::hit($rateKey, 60);

        $validated = $request->validate([
            'message' => ['nullable', 'required_without:image', 'string', 'max:2000'],
            'image' => ['nullable', 'required_without:message', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);
        $image = $validated['image'] ?? null;
        $attachmentPath = $image?->store('chat-attachments', 'local');

        if ($image && ! is_string($attachmentPath)) {
            throw new RuntimeException('Gagal menyimpan lampiran chat.');
        }

        $chatMessage = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => 'customer',
            'sender_id' => null,
            'sender_name' => $thread->customer_name,
            'message' => $validated['message'] ?? '',
            'attachment_path' => $attachmentPath,
            'is_read' => false,
        ]);

        $thread->increment('unread_admin_count');
        $thread->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chatMessage->id,
                'sender_type' => $chatMessage->sender_type,
                'sender_name' => $chatMessage->sender_name,
                'message' => $chatMessage->message,
                'attachment_url' => $chatMessage->attachment_path
                    ? route('customer.chat.attachment', $chatMessage, false)
                    : null,
                'created_at' => $chatMessage->created_at->format('H:i'),
                'created_date' => $chatMessage->created_at->format('d M Y'),
                'is_mine' => true,
                'is_read' => (bool) $chatMessage->is_read,
            ],
        ]);
    }

    /**
     * Polling pesan baru khusus untuk thread sesi aktif yang terverifikasi.
     */
    public function pollMessages(Request $request): JsonResponse
    {
        $sessionThreadId = session('customer_active_thread_id');
        if (! $sessionThreadId) {
            return response()->json(['messages' => [], 'thread' => null]);
        }

        $thread = ChatThread::find($sessionThreadId);
        if (! $thread) {
            return response()->json(['messages' => [], 'thread' => null]);
        }

        $lastId = (int) $request->query('last_id', 0);

        $messagesQuery = $thread->messages()->orderBy('id', 'asc');
        if ($lastId > 0) {
            $messagesQuery->where('id', '>', $lastId);
        }

        $messages = $messagesQuery->get();

        // Tandai pesan dari admin/staff sebagai dibaca oleh customer jika ada
        $this->markSellerMessagesAsRead($thread);

        $plainToken = $this->resolveSessionPlainToken($thread);

        return response()->json([
            'thread' => [
                'id' => $thread->id,
                'customer_name' => $thread->customer_name,
                'thread_type' => $thread->thread_type,
                'order_code' => $thread->order_code,
                'token' => $plainToken,
                'secret_url' => $thread->getCustomerSecretUrl($plainToken),
            ],
            'messages' => $messages->map(fn (ChatMessage $msg) => [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_name,
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_path
                    ? route('customer.chat.attachment', $msg, false)
                    : null,
                'created_at' => $msg->created_at->format('H:i'),
                'created_date' => $msg->created_at->format('d M Y'),
                'is_mine' => $msg->sender_type === 'customer',
                'is_read' => (bool) $msg->is_read,
            ]),
        ]);
    }

    /**
     * Tampilkan lampiran hanya jika sesi pembeli terverifikasi untuk thread pesan ini.
     */
    public function attachment(ChatMessage $chatMessage): BinaryFileResponse
    {
        $sessionThreadId = session('customer_active_thread_id');
        abort_unless($sessionThreadId && $chatMessage->thread_id === (int) $sessionThreadId, 404);

        $path = $chatMessage->attachment_path;
        abort_if($path === null || ! Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Simpan PIN yang sudah dikonfirmasi ke sesi checkout pembeli.
     */
    public function storePin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chat_pin' => ['required', 'digits:6'],
        ], [
            'chat_pin.required' => 'PIN rahasia chat wajib diisi.',
            'chat_pin.digits' => 'PIN rahasia chat harus tepat 6 digit angka.',
        ]);

        session(['customer_chat_pin' => $validated['chat_pin']]);

        return response()->json(['success' => true]);
    }

    /**
     * Akhiri sesi chat di browser.
     */
    public function endSession(Request $request): RedirectResponse
    {
        session()->forget([
            'customer_active_thread_id',
            'customer_plain_access_token',
            'customer_verified_phone',
            'customer_verified_order',
            'customer_chat_phone',
            'customer_chat_name',
            'customer_chat_order',
        ]);

        return redirect()->route('customer.chat.index');
    }

    /**
     * Ambil plain token dari sesi hanya jika hash-nya cocok dengan thread aktif.
     */
    private function resolveSessionPlainToken(ChatThread $thread): ?string
    {
        $sessionPlain = session('customer_plain_access_token');
        if (! is_string($sessionPlain) || $sessionPlain === '') {
            return null;
        }

        return hash_equals((string) $thread->access_token, ChatThread::hashAccessToken($sessionPlain))
            ? $sessionPlain
            : null;
    }

    /**
     * Tandai semua pesan dari admin/staf pada thread ini sebagai sudah dibaca pembeli.
     */
    private function markSellerMessagesAsRead(ChatThread $thread): void
    {
        $unreadSellerMessages = $thread->messages()
            ->whereIn('sender_type', ['admin', 'staff'])
            ->where('is_read', false);

        if ($unreadSellerMessages->exists()) {
            $unreadSellerMessages->update(['is_read' => true]);
            $thread->update(['unread_customer_count' => 0]);
        }
    }

    /**
     * Tanggapan gagal verifikasi yang aman.
     */
    private function verificationFailed(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['error' => $message], 422);
        }

        return back()
            ->withInput($request->only('customer_phone', 'order_code'))
            ->withErrors(['verification' => $message]);
    }
}
