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

#[Fillable([
    'kode_pesanan',
    'tipe_pesanan',
    'nama_pemesan',
    'nomor_wa',
    'alamat_lengkap',
    'opsi_pengiriman',
    'metode_pembayaran',
    'total_harga',
    'status',
    'catatan',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

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
     * Buat kode pesanan unik berformat ORD-YYYYMMDD-XXXX.
     */
    public static function generateKodePesanan(): string
    {
        do {
            $kode = sprintf('ORD-%s-%04d', now()->format('Ymd'), random_int(0, 9999));
        } while (static::query()->where('kode_pesanan', $kode)->exists());

        return $kode;
    }

    /**
     * Produk yang dipesan pada pesanan ini (relasi many-to-many via order_items).
     *
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['jumlah', 'subtotal', 'deskripsi_custom', 'foto_referensi', 'ukuran_kue', 'rasa_kue'])
            ->withTimestamps();
    }
}
