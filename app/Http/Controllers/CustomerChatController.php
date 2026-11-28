<?php

namespace App\Http\Controllers;

use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Http\Requests\StoreCustomOrderItemRequest;
use App\Models\CakeOption;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Order;
use App\Models\Product;
use App\Services\CustomOrderChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerChatController extends Controller
{
    public function __construct(public CustomOrderChatService $chatService) {}

    /**
     * Tampilkan halaman chat pembeli.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // 1. Jika pembeli membuka URL dengan ?token=..., arahkan ke verifikasi PIN
        // terlebih dahulu untuk thread pesanan yang dilindungi PIN.
        // Token hanya berlaku untuk thread inquiry (pra-order) tanpa PIN.
        $token = $request->query('token');
        if (! empty($token)) {
            $tokenThread = ChatThread::findByPlainToken((string) $token);

            if ($tokenThread) {
                if ($tokenThread->thread_type === 'order' && filled($tokenThread->order_code)) {
                    // Semua token link ke pesanan wajib melalui verifikasi PIN terlebih dahulu.
                    $linkedOrder = Order::where('kode_pesanan', $tokenThread->order_code)->first();
                    $intendedCode = $linkedOrder?->kode_pesanan ?? $tokenThread->order_code;

                    if (! $this->chatService->hasVerifiedOrderAccess($intendedCode)) {
                        // Belum ada session valid — paksa verifikasi ulang.
                        session(['customer_chat_intended_order' => $intendedCode]);

                        return redirect()
                            ->route('customer.chat.index', [
                                'mode' => 'order',
                                'order' => $intendedCode,
                            ])
                            ->withErrors([
                                'verification' => 'Untuk membuka ruang chat pesanan ini, silakan verifikasi Kode Pesanan, Nomor WhatsApp, dan PIN Anda.',
                            ]);
                    }

                    // Session sudah valid — tampilkan chat tanpa memperpanjang session.
                    if ($linkedOrder) {
                        $this->chatService->expireIfPastDue($linkedOrder);
                    }
                    $tokenThread->load(['messages' => fn ($query) => $query->orderBy('id')]);
                } else {
                    // Thread inquiry (pra-order) tidak memakai PIN — set session langsung.
                    session([
                        'customer_active_thread_id' => $tokenThread->id,
                        'customer_plain_access_token' => $tokenThread->plainAccessToken,
                        'customer_verified_phone' => $tokenThread->customer_phone,
                        'customer_verified_order' => null,
                        'customer_chat_phone' => $tokenThread->customer_phone,
                        'customer_chat_name' => $tokenThread->customer_name,
                        'customer_chat_order' => null,
                    ]);
                }

                $this->markSellerMessagesAsRead($tokenThread);

                return $this->renderChatView(
                    $request,
                    $tokenThread,
                    $tokenThread->customer_phone,
                    $tokenThread->customer_name,
                    $tokenThread->order_code,
                    $tokenThread->thread_type === 'order' ? 'order' : 'inquiry'
                );
            }
        }

        $requestedOrderCode = $request->filled('order') ? trim((string) $request->query('order')) : null;

        // 2. Jika pembeli meminta membuka alur verifikasi (misalnya mode=order atau verify=1/verify=3 dari form Custom Cake),
        // atau mengakses ?order=... yang belum terverifikasi pada session browser saat ini,
        // tahan akses isi chat dan arahkan ke form verifikasi Kode Pesanan + No. HP + PIN.
        $forceVerify = $request->filled('verify')
            || ($request->query('mode') === 'order' && ! $request->has('order'))
            || ($requestedOrderCode !== null && ! $this->chatService->hasVerifiedOrderAccess($requestedOrderCode));

        if ($request->filled('verify') || ($request->query('mode') === 'order' && ! $request->has('order'))) {
            session()->forget([
                'customer_active_thread_id',
                'customer_plain_access_token',
                'customer_verified_phone',
                'customer_verified_order',
                'customer_verified_orders',
                'customer_chat_order',
            ]);
        }

        if ($requestedOrderCode !== null && ! $this->chatService->hasVerifiedOrderAccess($requestedOrderCode)) {
            session(['customer_chat_intended_order' => strtoupper($requestedOrderCode)]);
        }

        $sessionThreadId = session('customer_active_thread_id');
        $thread = null;

        if (! $forceVerify) {
            if ($requestedOrderCode !== null && $this->chatService->hasVerifiedOrderAccess($requestedOrderCode)) {
                $matchedOrder = Order::where('kode_pesanan', $requestedOrderCode)->first()
                    ?? Order::whereRaw('LOWER(kode_pesanan) = ?', [strtolower($requestedOrderCode)])->first();

                if ($matchedOrder) {
                    $thread = ChatThread::with([
                        'messages' => fn ($query) => $query->orderBy('id'),
                    ])->where('order_code', $matchedOrder->kode_pesanan)->first();

                    // Session sudah valid — TIDAK panggil grantOrderChatSession() lagi.
                    // Session hanya boleh di-grant melalui verifyOrder() setelah PIN diverifikasi.
                }
            } elseif ($sessionThreadId) {
                $candidate = ChatThread::with([
                    'messages' => fn ($query) => $query->orderBy('id'),
                ])->find($sessionThreadId);

                if ($candidate) {
                    if ($candidate->thread_type === 'order' && filled($candidate->order_code)) {
                        if ($this->chatService->hasVerifiedOrderAccess($candidate->order_code)) {
                            $thread = $candidate;
                        } else {
                            $this->chatService->forgetCustomerChatSession();
                        }
                    } else {
                        $thread = $candidate;
                    }
                }
            }

            if ($thread) {
                $this->markSellerMessagesAsRead($thread);
                $thread->plainAccessToken = $this->resolveSessionPlainToken($thread);
            }
        }

        $presetPhone = $request->query('phone') ?? session('customer_chat_phone');
        $presetName = $request->query('name') ?? session('customer_chat_name');
        $presetOrder = $request->query('order') ?? session('customer_chat_order');
        $defaultMode = $request->query('mode', ! empty($presetOrder) ? 'order' : 'inquiry');

        return $this->renderChatView(
            $request,
            $thread,
            is_string($presetPhone) ? $presetPhone : null,
            is_string($presetName) ? $presetName : null,
            is_string($presetOrder) ? $presetOrder : null,
            is_string($defaultMode) ? $defaultMode : 'inquiry'
        );
    }

    /**
     * Tampilkan ruang chat pesanan melalui URL langsung (dilindungi middleware customer.chat.access).
     */
    public function showOrderChat(Request $request, string $kode_pesanan): View
    {
        $trimmedCode = trim($kode_pesanan);
        $order = Order::where('kode_pesanan', $trimmedCode)->first()
            ?? Order::whereRaw('LOWER(kode_pesanan) = ?', [strtolower($trimmedCode)])->firstOrFail();
        $this->chatService->expireIfPastDue($order);

        $thread = ChatThread::with([
            'messages' => fn ($query) => $query->orderBy('id'),
        ])->where('order_code', $order->kode_pesanan)->firstOrFail();

        $this->markSellerMessagesAsRead($thread);
        $thread->plainAccessToken = $this->resolveSessionPlainToken($thread);

        return $this->renderChatView(
            $request,
            $thread,
            $thread->customer_phone,
            $thread->customer_name,
            $thread->order_code,
            'order'
        );
    }

    /**
     * Mode 1: Verifikasi 2 Langkah (Nomor HP + Kode Pesanan + PIN) untuk membuka chat pesanan.
     */
    public function verifyOrder(Request $request): JsonResponse|RedirectResponse
    {
        // Rate limiting untuk mencegah brute force percobaan kombinasi kode pesanan, nomor WA, & PIN
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
        $order = Order::where('kode_pesanan', $orderCode)->first()
            ?? Order::whereRaw('LOWER(kode_pesanan) = ?', [strtolower($orderCode)])->first();

        if (! $order) {
            return $this->verificationFailed($request, 'Kombinasi Nomor WhatsApp, Kode Pesanan, dan PIN tidak cocok.');
        }

        $orderPhoneNormalized = ChatThread::normalizePhone($order->nomor_wa);

        if ($orderPhoneNormalized !== $normalizedPhone || ! $order->verifyChatPin($validated['chat_pin'])) {
            return $this->verificationFailed($request, 'Kombinasi Nomor WhatsApp, Kode Pesanan, dan PIN tidak cocok.');
        }

        RateLimiter::clear($rateKey);

        $this->chatService->expireIfPastDue($order);

        // 2. Cari atau buat thread chat untuk pesanan ini
        $thread = ChatThread::where('order_code', $order->kode_pesanan)->first();

        if (! $thread) {
            $thread = ChatThread::create([
                'customer_name' => $order->nama_pemesan,
                'customer_phone' => $normalizedPhone,
                'thread_type' => 'order',
                'order_code' => $order->kode_pesanan,
                'status' => $this->resolveThreadStatusFromOrder($order),
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
                'status' => $this->resolveThreadStatusFromOrder($order),
            ]);
            $thread->rotateAccessToken();
        }

        // 3. Simpan penanda izin akses & token verifikasi ke sesi browser
        $this->chatService->grantOrderChatSession($thread, $order, $normalizedPhone);

        // Jika ada pesan awal dan pesanan belum ditutup/kedaluwarsa
        if (! blank($validated['message'] ?? null) && ! $this->isOrderClosedForMessaging($order)) {
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

        $intendedOrderCode = session()->pull('customer_chat_intended_order');
        $redirectTarget = is_string($intendedOrderCode) && strcasecmp($intendedOrderCode, $order->kode_pesanan) === 0
            ? route('customer.chat.order', $order->kode_pesanan)
            : route('customer.chat.index');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'thread_id' => $thread->id,
                'token' => $thread->plainAccessToken,
                'secret_url' => $thread->getCustomerSecretUrl(),
                'chat_url' => route('customer.chat.order', $order->kode_pesanan),
            ]);
        }

        return redirect()->to($redirectTarget);
    }

    /**
     * Mode 2: Chat Baru Pra-Order / Tanya-tanya (Tanpa Kode Pesanan).
     * Aturan keamanan:
     * - Tolak nomor penjual/admin.
     * - Tolak jika nomor HP masih punya pesanan custom aktif dan arahkan ke verifikasi PIN.
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

            return back()->withInput()->withErrors(['customer_phone' => 'Nomor HP/WhatsApp tidak valid.']);
        }

        if ($this->chatService->isSellerPhone($normalizedPhone)) {
            $msg = 'Nomor WhatsApp ini terdaftar sebagai nomor penjual/admin dan tidak dapat digunakan untuk membuat ruang chat pembeli.';
            if ($request->wantsJson()) {
                return response()->json(['error' => $msg], 422);
            }

            return back()->withInput()->withErrors(['customer_phone' => $msg]);
        }

        if ($activeOrder = $this->chatService->findActiveCustomOrderForPhone($normalizedPhone)) {
            // Selalu arahkan ke form verifikasi PIN terlepas dari status session saat ini.
            // Pembeli WAJIB memasukkan Kode Pesanan + No. HP + PIN untuk masuk ke ruang chat.
            $reentryUrl = route('customer.chat.index', [
                'mode' => 'order',
                'verify' => 1,
                'phone' => $validated['customer_phone'],
            ]);
            $msg = 'Nomor WhatsApp ini masih memiliki pesanan custom aktif yang belum selesai atau di-ACC. Silakan masuk ke ruang chat aktif menggunakan Kode Pesanan, Nomor HP, dan PIN.';

            if ($request->wantsJson()) {
                return response()->json([
                    'error' => $msg,
                    'requires_verification' => true,
                    'redirect_url' => $reentryUrl,
                ], 422);
            }

            return back()
                ->withInput()
                ->with('active_order_chat_url', $reentryUrl)
                ->with('active_order_requires_verification', true)
                ->withErrors(['customer_phone' => $msg]);
        }

        // Buat thread terisolasi khusus inquiry pra-order baru
        $thread = ChatThread::create([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $normalizedPhone,
            'thread_type' => 'inquiry',
            'order_code' => null,
            'status' => 'active',
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
     * Tambahkan item kue kustom baru ke pesanan aktif di dalam ruang chat.
     */
    public function addCustomItem(StoreCustomOrderItemRequest $request): JsonResponse|RedirectResponse
    {
        $sessionThreadId = session('customer_active_thread_id');
        if (! $sessionThreadId) {
            return response()->json(['error' => 'Sesi chat belum terverifikasi atau telah berakhir.'], 403);
        }

        $thread = ChatThread::find($sessionThreadId);
        if (! $thread || $thread->thread_type !== 'order' || blank($thread->order_code)) {
            return response()->json(['error' => 'Penambahan item hanya tersedia di ruang chat pesanan custom aktif.'], 422);
        }

        $order = Order::where('kode_pesanan', $thread->order_code)->first();
        if (! $order || $order->tipe_pesanan !== TipePesanan::Custom) {
            return response()->json(['error' => 'Pesanan ini bukan pesanan custom cake.'], 422);
        }

        try {
            $result = $this->chatService->addCustomItemToOrder(
                $order,
                $thread,
                $request->validated(),
                $request->file('foto_referensi')
            );
        } catch (ValidationException $exception) {
            $firstMessage = collect($exception->errors())->flatten()->first() ?? 'Gagal menambahkan item kue custom.';
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => $firstMessage,
                    'errors' => $exception->errors(),
                ], 422);
            }

            return back()->withErrors($exception->errors());
        }

        $updatedOrder = $result['order'];
        $chatMessage = $result['message'];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => $this->formatOrderSummary($updatedOrder),
                'item' => [
                    'id' => $result['item']->id,
                    'jumlah' => $result['item']->jumlah,
                    'subtotal' => $result['item']->subtotal,
                    'ukuran_kue' => $result['item']->ukuran_kue,
                    'deskripsi_custom' => $result['item']->deskripsi_custom,
                ],
                'message' => $this->formatCustomerMessage($chatMessage),
            ], 201);
        }

        return redirect()
            ->route('customer.chat.order', $updatedOrder->kode_pesanan)
            ->with('success', 'Item kue custom baru berhasil ditambahkan ke pesanan Anda.');
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

        $order = null;
        if ($thread->thread_type === 'order' && filled($thread->order_code)) {
            $order = Order::where('kode_pesanan', $thread->order_code)->first();
            if ($order) {
                $this->chatService->expireIfPastDue($order);
                if ($this->isOrderClosedForMessaging($order)) {
                    return response()->json([
                        'error' => 'Diskusi pesanan ini telah ditutup, dibatalkan, atau kedaluwarsa.',
                    ], 422);
                }
            }
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

        if ($order && $order->isCustomActive()) {
            $expiryHours = max(1, (int) config('custom_order.expiry_hours', 24));
            $order->update(['expires_at' => now()->addHours($expiryHours)]);
        }

        return response()->json([
            'success' => true,
            'message' => $this->formatCustomerMessage($chatMessage),
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

        $order = null;
        if ($thread->thread_type === 'order' && filled($thread->order_code)) {
            $order = Order::with(['items.product'])->where('kode_pesanan', $thread->order_code)->first();
            if ($order) {
                $this->chatService->expireIfPastDue($order);
            }
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
                'status' => $thread->status ?? 'active',
                'token' => $plainToken,
                'secret_url' => $thread->getCustomerSecretUrl($plainToken),
            ],
            'order' => $order ? $this->formatOrderSummary($order) : null,
            'messages' => $messages->map(fn (ChatMessage $msg) => $this->formatCustomerMessage($msg)),
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
        $this->chatService->forgetCustomerChatSession();

        return redirect()->route('customer.chat.index');
    }

    private function renderChatView(
        Request $request,
        ?ChatThread $thread,
        ?string $presetPhone,
        ?string $presetName,
        ?string $presetOrder,
        string $defaultMode
    ): View {
        $activeOrder = null;
        if ($thread && $thread->thread_type === 'order' && filled($thread->order_code)) {
            $activeOrder = Order::with(['items.product'])
                ->where('kode_pesanan', $thread->order_code)
                ->first();

            if ($activeOrder && $this->chatService->expireIfPastDue($activeOrder)) {
                $thread->load(['messages' => fn ($query) => $query->orderBy('id')]);
            }
        }

        $isCustomOrder = $activeOrder !== null && $activeOrder->tipe_pesanan === TipePesanan::Custom;
        $canAddCustomItems = $isCustomOrder && $activeOrder->isCustomActive();

        return view('customer.chat.index', [
            'thread' => $thread,
            'activeOrder' => $activeOrder,
            'canAddCustomItems' => $canAddCustomItems,
            'isClosedOrder' => $activeOrder !== null && $this->isOrderClosedForMessaging($activeOrder),
            'cakeOptions' => $canAddCustomItems ? CakeOption::with('category')->orderBy('bentuk')->orderBy('ukuran')->get() : collect(),
            'categoriesWithOptions' => $canAddCustomItems ? Category::query()->whereHas('cakeOptions')->orderBy('nama_kategori')->get() : collect(),
            'products' => $canAddCustomItems ? Product::query()->with('category')->orderBy('nama_kue')->get() : collect(),
            'maxCustomQuantity' => CustomOrderChatService::MAX_CUSTOM_QUANTITY,
            'presetPhone' => $presetPhone,
            'presetName' => $presetName,
            'presetOrder' => $presetOrder,
            'defaultMode' => $defaultMode,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatCustomerMessage(ChatMessage $msg): array
    {
        return [
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
            'is_system' => $msg->sender_type === 'system',
            'is_read' => (bool) $msg->is_read,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatOrderSummary(Order $order): array
    {
        return [
            'kode_pesanan' => $order->kode_pesanan,
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'is_custom' => $order->tipe_pesanan === TipePesanan::Custom,
            'is_active' => $order->isCustomActive(),
            'is_closed' => $this->isOrderClosedForMessaging($order),
            'total_harga' => $order->total_harga,
            'total_harga_formatted' => 'Rp '.number_format($order->total_harga, 0, ',', '.'),
            'items_count' => $order->items()->count(),
            'total_qty' => (int) $order->items()->sum('jumlah'),
            'expires_at_formatted' => $order->expires_at?->format('d M Y H:i'),
            'cancel_reason' => $order->cancel_reason,
        ];
    }

    private function isOrderClosedForMessaging(Order $order): bool
    {
        return in_array($order->status, [
            StatusPesanan::Batal,
            StatusPesanan::Ditolak,
            StatusPesanan::Expired,
        ], true);
    }

    private function resolveThreadStatusFromOrder(Order $order): string
    {
        return match ($order->status) {
            StatusPesanan::Proses => 'approved',
            StatusPesanan::Selesai => 'completed',
            StatusPesanan::Batal, StatusPesanan::Ditolak => 'cancelled',
            StatusPesanan::Expired => 'expired',
            default => 'active',
        };
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
     * Tandai semua pesan dari admin/staf/sistem pada thread ini sebagai sudah dibaca pembeli.
     */
    private function markSellerMessagesAsRead(ChatThread $thread): void
    {
        $unreadSellerMessages = $thread->messages()
            ->whereIn('sender_type', ['admin', 'staff', 'system'])
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
