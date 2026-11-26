<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminChatController extends Controller
{
    /**
     * Tampilkan antarmuka inbox chat (digunakan bersama oleh Admin dan Staf).
     */
    public function index(Request $request): View
    {
        $threads = ChatThread::query()
            ->with(['latestMessage'])
            ->orderByDesc('last_message_at')
            ->get();

        $activeThread = null;
        $activeThreadRelations = [
            'messages' => fn ($query) => $query->with('sender')->orderBy('id'),
        ];

        if ($request->filled('thread_id')) {
            $activeThread = ChatThread::with($activeThreadRelations)->find($request->input('thread_id'));
        } elseif ($threads->isNotEmpty()) {
            $activeThread = ChatThread::with($activeThreadRelations)->find($threads->first()->id);
        }

        if ($activeThread) {
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
            'totalUnread' => $totalUnread,
            'panel' => auth()->user()->isStaff() ? 'staff' : 'admin',
        ]);
    }

    /**
     * Polling inbox threads dan jumlah pesan belum dibaca.
     */
    public function pollThreads(Request $request): JsonResponse
    {
        $threads = ChatThread::query()
            ->with(['latestMessage'])
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

        return response()->json([
            'thread_id' => $thread->id,
            'customer_name' => $thread->customer_name,
            'customer_phone' => $thread->customer_phone,
            'order_code' => $thread->order_code,
            'messages' => $messages->map(fn (ChatMessage $msg) => [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_name,
                'sender_badge' => $msg->sender_type === 'admin'
                    ? 'Admin'
                    : ($msg->sender_type === 'staff' ? 'Staf - '.$msg->sender_name : null),
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_path
                    ? route($request->user()->isStaff() ? 'staff.chat.attachment' : 'admin.chat.attachment', $msg, false)
                    : null,
                'created_at' => $msg->created_at->format('H:i'),
                'created_date' => $msg->created_at->format('d M Y'),
                'is_internal' => in_array($msg->sender_type, ['admin', 'staff'], true),
            ]),
        ]);
    }

    /**
     * Kirim balasan penjual (admin atau staf berizin).
     */
    public function reply(Request $request, ChatThread $thread): JsonResponse
    {
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
            ],
        ]);
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
}
