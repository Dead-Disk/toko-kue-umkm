<?php

namespace App\Models;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

#[Fillable([
    'kode_pesanan',
    'tipe_pesanan',
    'nama_pemesan',
    'nomor_wa',
    'nomor_wa_normalized',
    'chat_pin',
    'alamat_lengkap',
    'opsi_pengiriman',
    'metode_pembayaran',
    'total_harga',
    'status',
    'catatan',
    'bukti_pembayaran',
    'tanggal_ambil',
    'expires_at',
    'acc_at',
    'cancelled_at',
    'expired_at',
    'cancel_reason',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * Sinkronkan nomor_wa_normalized setiap kali order disimpan.
     */
    protected static function booted(): void
    {
        static::saving(function (Order $order): void {
            $normalized = ChatThread::normalizePhone($order->nomor_wa);
            $order->nomor_wa_normalized = $normalized !== '' ? $normalized : null;
        });
    }

    /**
     * Sembunyikan hash PIN chat dari serialisasi array/JSON.
     *
     * @var list<string>
     */
    protected $hidden = [
        'chat_pin',
    ];

    /**
     * Periksa apakah PIN chat yang dimasukkan pembeli cocok dengan hash yang tersimpan.
     */
    public function verifyChatPin(string $pin): bool
    {
        if (blank($this->chat_pin)) {
            return false;
        }

        return Hash::check($pin, $this->chat_pin);
    }

    /**
     * Apakah pesanan custom ini masih dalam status aktif/diskusi (belum ACC 100%, selesai, batal, ditolak, atau expired).
     */
    public function isCustomActive(): bool
    {
        if ($this->tipe_pesanan !== TipePesanan::Custom) {
            return false;
        }

        if ($this->status !== StatusPesanan::Pending || $this->acc_at !== null) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Apakah pesanan ini sudah melewati batas kedaluwarsa diskusi custom.
     */
    public function isCustomPastDue(?int $expiryHours = null): bool
    {
        if ($this->tipe_pesanan !== TipePesanan::Custom) {
            return false;
        }

        if ($this->status !== StatusPesanan::Pending || $this->acc_at !== null) {
            return false;
        }

        if ($this->expires_at !== null) {
            return $this->expires_at->isPast();
        }

        $hours = $expiryHours ?? (int) config('custom_order.expiry_hours', 24);

        return $this->created_at !== null && $this->created_at->copy()->addHours($hours)->isPast();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipe_pesanan' => TipePesanan::class,
            'opsi_pengiriman' => OpsiPengiriman::class,
            'metode_pembayaran' => MetodePembayaran::class,
            'status' => StatusPesanan::class,
            'total_harga' => 'integer',
            'tanggal_ambil' => 'date',
            'expires_at' => 'datetime',
            'acc_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Detail item pada pesanan ini (reguler maupun custom).
     *
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Pembayaran yang tercatat untuk pesanan ini.
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Thread chat yang terhubung dengan pesanan ini.
     *
     * @return HasOne<ChatThread, $this>
     */
    public function chatThread(): HasOne
    {
        return $this->hasOne(ChatThread::class, 'order_code', 'kode_pesanan');
    }

    /**
     * Buat kode pesanan unik berformat ORD-YYYYMMDD-XXXX.
     * Pesanan soft-deleted ikut diperiksa karena kodenya masih menempati unique index.
     */
    public static function generateKodePesanan(): string
    {
        do {
            $kode = sprintf('ORD-%s-%04d', now()->format('Ymd'), random_int(0, 9999));
        } while (static::withTrashed()->where('kode_pesanan', $kode)->exists());

        return $kode;
    }

    /**
     * Teks ringkasan pesanan yang dikirim pembeli ke WhatsApp penjual.
     */
    public function whatsappMessage(): string
    {
        $rupiah = fn (int $nominal): string => 'Rp'.number_format($nominal, 0, ',', '.');

        $baris = [
            'Halo, saya sudah membuat pesanan dengan rincian berikut:',
            '',
            "*Kode Pesanan:* {$this->kode_pesanan}",
            '',
            "*Nama:* {$this->nama_pemesan}",
            "*No HP / WA:* {$this->nomor_wa}",
            "*Alamat:* {$this->alamat_lengkap}",
            "*Metode Pembayaran:* {$this->metode_pembayaran->label()}",
            "*Opsi Pengiriman:* {$this->opsi_pengiriman->label()}",
        ];

        if ($this->tanggal_ambil) {
            $baris[] = "*Tanggal Ambil:* {$this->tanggal_ambil->format('d/m/Y')}";
        }

        $baris[] = '';
        $baris[] = '*Item Pesanan:*';

        foreach ($this->items as $nomor => $item) {
            $hargaSatuan = intdiv($item->subtotal, max($item->jumlah, 1));

            $baris[] = sprintf('%d. %s', $nomor + 1, $item->product?->nama_kue ?? 'Kue Custom');
            $baris[] = sprintf('   %d x %s = %s', $item->jumlah, $rupiah($hargaSatuan), $rupiah($item->subtotal));

            if (filled($item->catatan)) {
                $baris[] = "   Catatan: {$item->catatan}";
            }
        }

        $baris[] = '';
        $baris[] = "*Total Harga: {$rupiah($this->total_harga)}*";

        return implode("\n", $baris);
    }

    /**
     * Tautan wa.me berisi ringkasan pesanan, atau null bila nomor penjual belum diatur.
     */
    public function whatsappUrl(?string $nomorPenjual): ?string
    {
        $nomor = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', (string) $nomorPenjual));

        if (blank($nomor)) {
            return null;
        }

        return "https://wa.me/{$nomor}?text=".rawurlencode($this->whatsappMessage());
    }

    /**
     * Produk yang dipesan pada pesanan ini (relasi many-to-many via order_items).
     *
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['jumlah', 'subtotal', 'catatan', 'deskripsi_custom', 'foto_referensi', 'ukuran_kue', 'rasa_kue'])
            ->withTimestamps();
    }
}
