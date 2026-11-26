<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        // Jika pembeli meminta membuka alur verifikasi (misalnya mode=order atau verify=1 dari form Custom Cake),
        // paksa reset sesi thread aktif agar wajib verifikasi Nomor HP + Kode Pesanan terlebih dahulu.
        $forceVerify = $request->boolean('verify') || ($request->query('mode') === 'order' && ! $request->has('order'));

        if ($forceVerify) {
            session()->forget([
                'customer_active_thread_id',
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
        $validated = $request->validate([
            'customer_phone' => ['required', 'string', 'max:30'],
            'order_code' => ['required', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $normalizedPhone = ChatThread::normalizePhone($validated['customer_phone']);
        $orderCode = trim($validated['order_code']);

        // 1. Verifikasi kecocokan nomor HP dan Kode Pesanan terhadap tabel orders
        $order = Order::where('kode_pesanan', $orderCode)->first();

        if (! $order) {
            return $this->verificationFailed($request, 'Kombinasi Nomor WhatsApp dan Kode Pesanan tidak ditemukan.');
        }

        $orderPhoneNormalized = ChatThread::normalizePhone($order->nomor_wa);

        if ($orderPhoneNormalized !== $normalizedPhone) {
            return $this->verificationFailed($request, 'Kombinasi Nomor WhatsApp dan Kode Pesanan tidak cocok.');
        }

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
            // Pastikan data thread sinkron
            $thread->update([
                'customer_name' => $order->nama_pemesan,
                'customer_phone' => $normalizedPhone,
                'thread_type' => 'order',
            ]);
        }

        // 3. Simpan token verifikasi ke sesi browser
        session([
            'customer_active_thread_id' => $thread->id,
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
            ]);
        }

        return redirect()->route('customer.chat.index');
    }

    /**
     * Mode 2: Chat Baru Pra-Order / Tanya-tanya (Tanpa Kode Pesanan).
     * Aturan keamanan: membuat thread obrolan baru terisolasi ('inquiry'),
     * tidak boleh memuat riwayat thread pesanan lama.
     */
    public function startInquiry(Request $request): JsonResponse|RedirectResponse
    {
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

        // Simpan sesi aktif
        session([
            'customer_active_thread_id' => $thread->id,
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
        $adminStaffMessages = $thread->messages()
            ->whereIn('sender_type', ['admin', 'staff'])
            ->where('is_read', false);

        if ($adminStaffMessages->exists()) {
            $adminStaffMessages->update(['is_read' => true]);
            $thread->update(['unread_customer_count' => 0]);
        }

        return response()->json([
            'thread' => [
                'id' => $thread->id,
                'customer_name' => $thread->customer_name,
                'thread_type' => $thread->thread_type,
                'order_code' => $thread->order_code,
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
     * Akhiri sesi chat di browser.
     */
    public function endSession(Request $request): RedirectResponse
    {
        session()->forget([
            'customer_active_thread_id',
            'customer_verified_phone',
            'customer_verified_order',
            'customer_chat_phone',
            'customer_chat_name',
            'customer_chat_order',
        ]);

        return redirect()->route('customer.chat.index');
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
