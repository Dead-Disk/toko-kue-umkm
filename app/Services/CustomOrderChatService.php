<?php

namespace App\Services;

use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\CakeOption;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CustomOrderChatService
{
    public const MAX_CUSTOM_QUANTITY = 5;

    /**
     * Kumpulkan seluruh nomor HP penjual/admin/konfigurasi toko dalam format ternormalisasi.
     *
     * @return list<string>
     */
    public function getBlacklistedSellerPhones(): array
    {
        $rawPhones = collect();

        $rawPhones = $rawPhones->merge(
            StoreSetting::query()->whereNotNull('store_phone')->pluck('store_phone')
        );

        $rawPhones = $rawPhones->merge(
            User::query()
                ->whereIn('role', ['owner', 'admin', 'staff'])
                ->whereNotNull('phone')
                ->pluck('phone')
        );

        $rawPhones = $rawPhones->merge((array) config('custom_order.seller_phones', []));

        return $rawPhones
            ->map(static fn (mixed $phone): string => ChatThread::normalizePhone(is_string($phone) ? $phone : null))
            ->filter(static fn (string $phone): bool => $phone !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Periksa apakah nomor HP termasuk nomor penjual/admin yang diblacklist.
     */
    public function isSellerPhone(?string $phone): bool
    {
        $normalized = ChatThread::normalizePhone($phone);
        if ($normalized === '') {
            return false;
        }

        return in_array($normalized, $this->getBlacklistedSellerPhones(), true);
    }

    /**
     * Kedaluwarsakan seluruh pesanan custom pending yang sudah melewati batas waktu diskusi.
     */
    public function expireStaleCustomOrders(?string $normalizedPhone = null, ?int $onlyOrderId = null): int
    {
        $expiryHours = (int) config('custom_order.expiry_hours', 24);
        $fallbackCutoff = now()->subHours(max(1, $expiryHours));

        $candidateIds = Order::query()
            ->where('tipe_pesanan', TipePesanan::Custom)
            ->where('status', StatusPesanan::Pending)
            ->whereNull('acc_at')
            ->when($normalizedPhone !== null && $normalizedPhone !== '', function ($query) use ($normalizedPhone): void {
                $query->where('nomor_wa_normalized', $normalizedPhone);
            })
            ->when($onlyOrderId !== null, function ($query) use ($onlyOrderId): void {
                $query->where('id', $onlyOrderId);
            })
            ->where(function ($query) use ($fallbackCutoff): void {
                $query->where(function ($q): void {
                    $q->whereNotNull('expires_at')->where('expires_at', '<=', now());
                })->orWhere(function ($q) use ($fallbackCutoff): void {
                    $q->whereNull('expires_at')->where('created_at', '<=', $fallbackCutoff);
                });
            })
            ->pluck('id');

        if ($candidateIds->isEmpty()) {
            return 0;
        }

        $expiredCount = 0;

        foreach ($candidateIds as $orderId) {
            DB::transaction(function () use ($orderId, $expiryHours, &$expiredCount): void {
                $order = Order::query()->lockForUpdate()->find($orderId);

                if (! $order || ! $order->isCustomPastDue($expiryHours)) {
                    return;
                }

                $now = now();
                $reason = 'Diskusi pesanan custom kedaluwarsa otomatis karena melewati batas waktu konfirmasi.';

                $order->update([
                    'status' => StatusPesanan::Expired,
                    'expired_at' => $now,
                    'cancel_reason' => $reason,
                ]);

                $thread = ChatThread::where('order_code', $order->kode_pesanan)->first();
                if ($thread) {
                    $thread->update([
                        'status' => 'expired',
                        'closed_at' => $now,
                        'last_message_at' => $now,
                    ]);

                    ChatMessage::create([
                        'thread_id' => $thread->id,
                        'sender_type' => 'system',
                        'sender_id' => null,
                        'sender_name' => 'Sistem',
                        'message' => "⏰ Pesanan custom #{$order->kode_pesanan} telah kedaluwarsa otomatis karena melewati batas waktu diskusi. Kunci nomor HP Anda telah dibuka kembali sehingga Anda dapat membuat pesanan baru.",
                        'is_read' => false,
                    ]);

                    $thread->increment('unread_customer_count');
                }

                $expiredCount++;
            });
        }

        return $expiredCount;
    }

    /**
     * Jalankan pengecekan kedaluwarsa untuk satu pesanan tertentu jika sudah melewati batas waktu.
     */
    public function expireIfPastDue(Order $order): bool
    {
        if (! $order->isCustomPastDue()) {
            return false;
        }

        $expired = $this->expireStaleCustomOrders(onlyOrderId: $order->id) > 0;

        if ($expired) {
            $order->refresh();
        }

        return $expired;
    }

    /**
     * Cari pesanan custom aktif (belum ACC 100%, belum selesai, belum batal/ditolak/expired) untuk nomor HP tertentu.
     */
    public function findActiveCustomOrderForPhone(?string $phone): ?Order
    {
        $normalized = ChatThread::normalizePhone($phone);
        if ($normalized === '') {
            return null;
        }

        $this->expireStaleCustomOrders($normalized);

        return Order::query()
            ->where('tipe_pesanan', TipePesanan::Custom)
            ->where('status', StatusPesanan::Pending)
            ->whereNull('acc_at')
            ->where('nomor_wa_normalized', $normalized)
            ->latest('id')
            ->first();
    }

    /**
     * Tambahkan item kue kustom baru ke dalam pesanan custom aktif melalui ruang chat.
     *
     * @param  array{cake_option_id: int, base_product_id?: int|null, jumlah: int, deskripsi_custom: string}  $data
     * @return array{order: Order, item: OrderItem, message: ChatMessage}
     */
    public function addCustomItemToOrder(
        Order $order,
        ChatThread $thread,
        array $data,
        ?UploadedFile $photo = null
    ): array {
        $photoPath = null;
        if ($photo !== null) {
            $storedPath = $photo->store('custom_cakes', 'local');
            if (! is_string($storedPath)) {
                throw new RuntimeException('Gagal menyimpan foto referensi kue custom.');
            }
            $photoPath = $storedPath;
        }

        try {
            return DB::transaction(function () use ($order, $thread, $data, $photoPath): array {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

                if ($lockedOrder->isCustomPastDue()) {
                    $this->expireIfPastDue($lockedOrder);
                    throw ValidationException::withMessages([
                        'order' => 'Diskusi pesanan ini telah kedaluwarsa. Silakan buat pesanan custom baru.',
                    ]);
                }

                if (! $lockedOrder->isCustomActive()) {
                    throw ValidationException::withMessages([
                        'order' => 'Pesanan custom ini sudah difinalisasi atau ditutup sehingga tidak dapat menambah item baru.',
                    ]);
                }

                $cakeOption = CakeOption::with('category')->findOrFail((int) $data['cake_option_id']);
                $baseProduct = ! empty($data['base_product_id'])
                    ? Product::find((int) $data['base_product_id'])
                    : null;

                $jumlah = (int) $data['jumlah'];
                $hargaAcuan = $baseProduct?->harga ?? $cakeOption->harga_dasar;
                $subtotal = $hargaAcuan * $jumlah;

                $item = $lockedOrder->items()->create([
                    'product_id' => $baseProduct?->id,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                    'deskripsi_custom' => $data['deskripsi_custom'],
                    'foto_referensi' => $photoPath,
                    'ukuran_kue' => $cakeOption->label(),
                ]);

                $newTotal = (int) $lockedOrder->items()->sum('subtotal');
                $expiryHours = (int) config('custom_order.expiry_hours', 24);

                $lockedOrder->update([
                    'total_harga' => $newTotal,
                    'expires_at' => now()->addHours(max(1, $expiryHours)),
                ]);

                $categoryName = $cakeOption->category?->nama_kategori ?? 'Kue Custom';
                $productName = $baseProduct ? $baseProduct->nama_kue : '-';
                $formattedSubtotal = 'Rp '.number_format($subtotal, 0, ',', '.');
                $formattedTotal = 'Rp '.number_format($newTotal, 0, ',', '.');

                $messageText = "➕ *[Tambah Item Kue Custom - {$lockedOrder->kode_pesanan}]*\n\n"
                    ."• *Kategori:* {$categoryName}\n"
                    ."• *Produk Acuan:* {$productName}\n"
                    ."• *Varian/Ukuran:* {$cakeOption->label()}\n"
                    ."• *Jumlah:* {$jumlah} pcs\n"
                    ."• *Deskripsi Request:* {$data['deskripsi_custom']}\n"
                    ."• *Estimasi Item:* {$formattedSubtotal}\n"
                    ."💰 *Estimasi Total Pesanan Sekarang:* {$formattedTotal}";

                $chatMessage = ChatMessage::create([
                    'thread_id' => $thread->id,
                    'sender_type' => 'customer',
                    'sender_id' => null,
                    'sender_name' => $lockedOrder->nama_pemesan,
                    'message' => $messageText,
                    'attachment_path' => $photoPath,
                    'is_read' => false,
                ]);

                $thread->increment('unread_admin_count');
                $thread->update(['last_message_at' => now()]);

                return [
                    'order' => $lockedOrder->fresh(['items.product']),
                    'item' => $item,
                    'message' => $chatMessage,
                ];
            });
        } catch (\Throwable $e) {
            if ($photoPath !== null && Storage::disk('local')->exists($photoPath)) {
                Storage::disk('local')->delete($photoPath);
            }

            throw $e;
        }
    }

    /**
     * Tandai pesanan custom sebagai ACC 100% (status -> proses), melepaskan kunci nomor HP.
     */
    public function approveCustomDiscussion(Order $order, ?ChatThread $thread = null): Order
    {
        return DB::transaction(function () use ($order, $thread): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            $lockedOrder->update([
                'status' => StatusPesanan::Proses,
                'acc_at' => $lockedOrder->acc_at ?? now(),
                'expires_at' => null,
                'cancel_reason' => null,
            ]);

            $targetThread = $thread ?? ChatThread::where('order_code', $lockedOrder->kode_pesanan)->first();
            if ($targetThread) {
                $targetThread->update([
                    'status' => 'approved',
                    'closed_at' => null,
                    'last_message_at' => now(),
                ]);

                ChatMessage::create([
                    'thread_id' => $targetThread->id,
                    'sender_type' => 'system',
                    'sender_id' => null,
                    'sender_name' => 'Sistem',
                    'message' => "✅ Pesanan #{$lockedOrder->kode_pesanan} telah di-ACC 100% oleh penjual dan mulai diproses. Nomor HP Anda kini bebas digunakan kembali untuk pesanan baru jika diperlukan.",
                    'is_read' => false,
                ]);

                $targetThread->increment('unread_customer_count');
            }

            return $lockedOrder->fresh();
        });
    }

    /**
     * Batalkan diskusi pesanan oleh penjual dan lepaskan kunci nomor HP.
     */
    public function cancelDiscussion(Order $order, ?ChatThread $thread = null, ?string $reason = null): Order
    {
        return DB::transaction(function () use ($order, $thread, $reason): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $statusLama = $lockedOrder->status;
            $finalReason = filled($reason) ? trim($reason) : 'Diskusi pesanan dibatalkan oleh penjual.';

            $lockedOrder->update([
                'status' => StatusPesanan::Batal,
                'cancelled_at' => now(),
                'expires_at' => null,
                'cancel_reason' => $finalReason,
            ]);

            if ($statusLama === StatusPesanan::Proses) {
                $lockedOrder->items()->each(function (OrderItem $item): void {
                    $item->product()->withTrashed()->first()?->increment('stok', $item->jumlah);
                });
            }

            $targetThread = $thread ?? ChatThread::where('order_code', $lockedOrder->kode_pesanan)->first();
            if ($targetThread) {
                $targetThread->update([
                    'status' => 'cancelled',
                    'closed_at' => now(),
                    'last_message_at' => now(),
                ]);

                ChatMessage::create([
                    'thread_id' => $targetThread->id,
                    'sender_type' => 'system',
                    'sender_id' => null,
                    'sender_name' => 'Sistem',
                    'message' => "❌ Diskusi pesanan #{$lockedOrder->kode_pesanan} telah dibatalkan oleh penjual ({$finalReason}). Kunci nomor HP Anda telah dilepas kembali.",
                    'is_read' => false,
                ]);

                $targetThread->increment('unread_customer_count');
            }

            return $lockedOrder->fresh();
        });
    }

    /**
     * Simpan izin akses chat pesanan yang sudah terverifikasi ke session browser.
     */
    public function grantOrderChatSession(ChatThread $thread, Order $order, string $normalizedPhone): void
    {
        $verifiedOrders = session('customer_verified_orders', []);
        if (! is_array($verifiedOrders)) {
            $verifiedOrders = [];
        }

        $verifiedOrders[$order->kode_pesanan] = [
            'thread_id' => $thread->id,
            'phone' => $normalizedPhone,
            'pin_fingerprint' => filled($order->chat_pin) ? hash('sha256', (string) $order->chat_pin) : null,
            'verified_at' => now()->timestamp,
        ];

        $payload = [
            'customer_active_thread_id' => $thread->id,
            'customer_verified_phone' => $normalizedPhone,
            'customer_verified_order' => $order->kode_pesanan,
            'customer_verified_orders' => $verifiedOrders,
            'customer_chat_phone' => $normalizedPhone,
            'customer_chat_name' => $order->nama_pemesan,
            'customer_chat_order' => $order->kode_pesanan,
        ];

        if ($thread->plainAccessToken) {
            $payload['customer_plain_access_token'] = $thread->plainAccessToken;
        }

        session($payload);
    }

    /**
     * Periksa apakah session browser saat ini memiliki izin akses valid ke ruang chat kode pesanan tertentu.
     */
    public function hasVerifiedOrderAccess(string $orderCode): bool
    {
        $trimmedCode = trim($orderCode);
        if ($trimmedCode === '') {
            return false;
        }

        $order = Order::where('kode_pesanan', $trimmedCode)->first()
            ?? Order::whereRaw('LOWER(kode_pesanan) = ?', [strtolower($trimmedCode)])->first();
        if (! $order) {
            return false;
        }

        $thread = ChatThread::where('order_code', $order->kode_pesanan)->first();
        if (! $thread) {
            return false;
        }

        $orderPhone = ChatThread::normalizePhone($order->nomor_wa);
        $verifiedOrders = session('customer_verified_orders', []);

        if (! is_array($verifiedOrders) || ! isset($verifiedOrders[$order->kode_pesanan])) {
            return false;
        }

        $grant = $verifiedOrders[$order->kode_pesanan];
        if (! is_array($grant)) {
            return false;
        }

        if (($grant['thread_id'] ?? null) !== $thread->id || ($grant['phone'] ?? null) !== $orderPhone) {
            return false;
        }

        if (filled($order->chat_pin)) {
            $expectedFingerprint = hash('sha256', (string) $order->chat_pin);

            return is_string($grant['pin_fingerprint'] ?? null)
                && hash_equals($expectedFingerprint, $grant['pin_fingerprint']);
        }

        return true;
    }

    /**
     * Hapus seluruh tanda verifikasi chat dari session browser pembeli.
     */
    public function forgetCustomerChatSession(): void
    {
        session()->forget([
            'customer_active_thread_id',
            'customer_plain_access_token',
            'customer_verified_phone',
            'customer_verified_order',
            'customer_verified_orders',
            'customer_chat_phone',
            'customer_chat_name',
            'customer_chat_order',
            'customer_chat_intended_order',
        ]);
    }
}
