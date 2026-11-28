<?php

namespace App\Http\Controllers;

use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\ActivityLog;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Order;
use App\Services\CustomOrderChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminChatController extends Controller
{
    public function __construct(public CustomOrderChatService $chatService) {}

    /**
     * Tampilkan antarmuka inbox chat (digunakan bersama oleh Admin dan Staf).
     */
    public function index(Request $request): View
    {
        $this->chatService->expireStaleCustomOrders();

        $threads = ChatThread::query()
            ->with(['latestMessage', 'order'])
            ->orderByDesc('last_message_at')
            ->get();

        $activeThread = null;
        $activeThreadRelations = [
            'messages' => fn ($query) => $query->with('sender')->orderBy('id'),
            'order.items.product',
        ];

        if ($request->filled('thread_id')) {
            $activeThread = ChatThread::with($activeThreadRelations)->find($request->input('thread_id'));
        } elseif ($threads->isNotEmpty()) {
            $activeThread = ChatThread::with($activeThreadRelations)->find($threads->first()->id);
        }

        if ($activeThread) {
            if ($activeThread->order) {
                $this->chatService->expireIfPastDue($activeThread->order);
                $activeThread->load($activeThreadRelations);
            }

            // Tandai unread admin sebagai 0 jika dibuka
            $activeThread->messages()
                ->where('sender_type', 'customer')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $activeThread->update(['unread_admin_count' => 0]);
        }

        $totalUnread = ChatThread::sum('unread_admin_count');

        return view('admin.chat.index', [
            'threads' => $threads,
            'activeThread' => $activeThread,
            'activeOrder' => $activeThread?->order,
            'totalUnread' => $totalUnread,
            'panel' => auth()->user()->isStaff() ? 'staff' : 'admin',
        ]);
    }

    /**
     * Polling inbox threads dan jumlah pesan belum dibaca.
     */
    public function pollThreads(Request $request): JsonResponse
    {
        $this->chatService->expireStaleCustomOrders();

        $threads = ChatThread::query()
            ->with(['latestMessage', 'order'])
            ->orderByDesc('last_message_at')
            ->get();

        $totalUnread = ChatThread::sum('unread_admin_count');

        return response()->json([
            'total_unread' => (int) $totalUnread,
            'threads' => $threads->map(fn (ChatThread $t) => [
                'id' => $t->id,
                'customer_name' => $t->customer_name,
                'customer_phone' => $t->customer_phone,
                'order_code' => $t->order_code,
                'order_status' => $t->order?->status?->value,
                'order_status_label' => $t->order?->status?->label(),
                'unread_admin_count' => (int) $t->unread_admin_count,
                'last_message' => $t->latestMessage?->message ?? '',
                'last_message_time' => $t->last_message_at ? $t->last_message_at->format('H:i') : '',
            ]),
        ]);
    }

    /**
     * Polling pesan dari thread tertentu.
     */
    public function pollMessages(Request $request, ChatThread $thread): JsonResponse
    {
        if ($thread->order_code) {
            $order = Order::where('kode_pesanan', $thread->order_code)->first();
            if ($order) {
                $this->chatService->expireIfPastDue($order);
            }
        }

        $lastId = (int) $request->query('last_id', 0);

        $messagesQuery = $thread->messages()->with('sender')->orderBy('id', 'asc');
        if ($lastId > 0) {
            $messagesQuery->where('id', '>', $lastId);
        }

        $messages = $messagesQuery->get();

        // Tandai pesan customer yang baru masuk sebagai terbaca jika penjual sedang aktif di thread ini
        $customerUnread = $thread->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false);

        if ($customerUnread->exists()) {
            $customerUnread->update(['is_read' => true]);
            $thread->update(['unread_admin_count' => 0]);
        }

        $thread->load('order');

        return response()->json([
            'thread_id' => $thread->id,
            'customer_name' => $thread->customer_name,
            'customer_phone' => $thread->customer_phone,
            'order_code' => $thread->order_code,
            'order_status' => $thread->order?->status?->value,
            'order_status_label' => $thread->order?->status?->label(),
            'messages' => $messages->map(fn (ChatMessage $msg) => $this->formatAdminMessage($request, $msg)),
        ]);
    }

    /**
     * Kirim balasan penjual (admin atau staf berizin).
     */
    public function reply(Request $request, ChatThread $thread): JsonResponse
    {
        $order = null;
        if ($thread->order_code) {
            $order = Order::where('kode_pesanan', $thread->order_code)->first();
            if ($order) {
                $this->chatService->expireIfPastDue($order);
                if (in_array($order->status, [StatusPesanan::Batal, StatusPesanan::Ditolak, StatusPesanan::Expired], true)) {
                    return response()->json([
                        'error' => 'Diskusi pesanan ini telah ditutup, dibatalkan, atau kedaluwarsa.',
                    ], 422);
                }
            }
        }

        $validated = $request->validate([
            'message' => ['nullable', 'required_without:image', 'string', 'max:2000'],
            'image' => ['nullable', 'required_without:message', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $user = $request->user();
        $senderType = $user->isAdmin() ? 'admin' : 'staff';
        $image = $validated['image'] ?? null;
        $attachmentPath = $image?->store('chat-attachments', 'local');

        if ($image && ! is_string($attachmentPath)) {
            throw new RuntimeException('Gagal menyimpan lampiran chat.');
        }

        $chatMessage = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => $senderType,
            'sender_id' => $user->id,
            'sender_name' => $user->name,
            'message' => $validated['message'] ?? '',
            'attachment_path' => $attachmentPath,
            'is_read' => false,
        ]);

        $thread->increment('unread_customer_count');
        $thread->update(['last_message_at' => now()]);

        if ($order && $order->isCustomActive()) {
            $expiryHours = max(1, (int) config('custom_order.expiry_hours', 24));
            $order->update(['expires_at' => now()->addHours($expiryHours)]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chatMessage->id,
                'sender_type' => $chatMessage->sender_type,
                'sender_name' => $chatMessage->sender_name,
                'sender_badge' => $chatMessage->sender_type === 'admin'
                    ? 'Admin'
                    : 'Staf - '.$chatMessage->sender_name,
                'message' => $chatMessage->message,
                'attachment_url' => $chatMessage->attachment_path
                    ? route($user->isStaff() ? 'staff.chat.attachment' : 'admin.chat.attachment', $chatMessage, false)
                    : null,
                'created_at' => $chatMessage->created_at->format('H:i'),
                'created_date' => $chatMessage->created_at->format('d M Y'),
                'is_internal' => true,
                'is_system' => false,
                'is_read' => (bool) $chatMessage->is_read,
            ],
        ]);
    }

    /**
     * ACC 100% pesanan custom dari ruang chat penjual dan lepaskan kunci nomor HP.
     */
    public function approve(Request $request, ChatThread $thread): JsonResponse|RedirectResponse
    {
        $order = $thread->order_code
            ? Order::where('kode_pesanan', $thread->order_code)->first()
            : null;

        if (! $order || $order->tipe_pesanan !== TipePesanan::Custom) {
            return $this->chatActionError($request, $thread, 'Hanya pesanan custom cake yang dapat di-ACC melalui ruang diskusi ini.');
        }

        $this->chatService->expireIfPastDue($order);

        if ($order->status !== StatusPesanan::Pending) {
            return $this->chatActionError($request, $thread, 'Pesanan ini tidak lagi berstatus menunggu konfirmasi.');
        }

        $this->chatService->approveCustomDiscussion($order, $thread);

        ActivityLog::record(
            'ubah',
            'ACC 100% pesanan custom #'.$order->kode_pesanan.' ('.$order->nama_pemesan.') via chat',
            $order
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => StatusPesanan::Proses->value,
                'message' => 'Pesanan berhasil di-ACC 100% dan kunci nomor HP telah dilepas.',
            ]);
        }

        return redirect()
            ->route($request->user()->isStaff() ? 'staff.chat.index' : 'admin.chat.index', ['thread_id' => $thread->id])
            ->with('success', "Pesanan #{$order->kode_pesanan} telah di-ACC 100% dan kunci nomor HP telah dilepas.");
    }

    /**
     * Batalkan diskusi pesanan oleh penjual dan lepaskan kunci nomor HP.
     */
    public function cancel(Request $request, ChatThread $thread): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $order = $thread->order_code
            ? Order::where('kode_pesanan', $thread->order_code)->first()
            : null;

        if (! $order) {
            return $this->chatActionError($request, $thread, 'Thread ini tidak terhubung dengan pesanan.');
        }

        if (in_array($order->status, [StatusPesanan::Batal, StatusPesanan::Ditolak, StatusPesanan::Expired, StatusPesanan::Selesai], true)) {
            return $this->chatActionError($request, $thread, 'Pesanan sudah berstatus final dan tidak dapat dibatalkan.');
        }

        $this->chatService->cancelDiscussion($order, $thread, $validated['reason'] ?? null);

        ActivityLog::record(
            'ubah',
            'Membatalkan diskusi pesanan #'.$order->kode_pesanan.' ('.$order->nama_pemesan.') via chat',
            $order
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => StatusPesanan::Batal->value,
                'message' => 'Diskusi pesanan berhasil dibatalkan dan kunci nomor HP telah dilepas.',
            ]);
        }

        return redirect()
            ->route($request->user()->isStaff() ? 'staff.chat.index' : 'admin.chat.index', ['thread_id' => $thread->id])
            ->with('success', "Diskusi pesanan #{$order->kode_pesanan} berhasil dibatalkan dan kunci nomor HP dilepas.");
    }

    /**
     * Tampilkan lampiran chat hanya melalui route inbox yang terproteksi.
     */
    public function attachment(ChatMessage $chatMessage): BinaryFileResponse
    {
        $path = $chatMessage->attachment_path;
        abort_if($path === null || ! Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatAdminMessage(Request $request, ChatMessage $msg): array
    {
        return [
            'id' => $msg->id,
            'sender_type' => $msg->sender_type,
            'sender_name' => $msg->sender_name,
            'sender_badge' => match ($msg->sender_type) {
                'admin' => 'Admin',
                'staff' => 'Staf - '.$msg->sender_name,
                'system' => 'Sistem',
                default => null,
            },
            'message' => $msg->message,
            'attachment_url' => $msg->attachment_path
                ? route($request->user()->isStaff() ? 'staff.chat.attachment' : 'admin.chat.attachment', $msg, false)
                : null,
            'created_at' => $msg->created_at->format('H:i'),
            'created_date' => $msg->created_at->format('d M Y'),
            'is_internal' => in_array($msg->sender_type, ['admin', 'staff'], true),
            'is_system' => $msg->sender_type === 'system',
            'is_read' => (bool) $msg->is_read,
        ];
    }

    private function chatActionError(Request $request, ChatThread $thread, string $message): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['error' => $message], 422);
        }

        return redirect()
            ->route($request->user()->isStaff() ? 'staff.chat.index' : 'admin.chat.index', ['thread_id' => $thread->id])
            ->withErrors(['chat_action' => $message]);
    }
}
