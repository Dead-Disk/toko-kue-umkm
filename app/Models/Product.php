<?php

namespace App\Models;

use App\Enums\StatusStok;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'category_id',
    'nama_kue',
    'slug',
    'deskripsi',
    'harga',
    'stok',
    'max_per_order',
    'status_stok',
    'foto',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['foto_url', 'short_deskripsi'];

    /**
     * Ringkasan deskripsi singkat (maksimal 60 karakter) untuk tabel dan katalog.
     */
    public function getShortDeskripsiAttribute(): string
    {
        return Str::limit($this->deskripsi ?? '', 60);
    }

    /**
     * Cek apakah produk memiliki file foto fisik atau URL yang benar-benar ada.
     */
    public function hasRealFoto(): bool
    {
        if (empty($this->foto)) {
            return false;
        }

        if (Storage::disk('public')->exists($this->foto)) {
            return true;
        }

        if (file_exists(public_path('storage/'.$this->foto))) {
            return true;
        }

        if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
            return true;
        }

        if (file_exists(public_path($this->foto))) {
            return true;
        }

        return false;
    }

    /**
     * URL publik lengkap untuk foto produk dengan fallback aman.
     * Mengembalikan null jika produk tidak memiliki foto fisik yang valid.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (! empty($this->foto)) {
            if (Storage::disk('public')->exists($this->foto)) {
                return route('product-images.show', ['path' => $this->foto]);
            }

            if (file_exists(public_path('storage/'.$this->foto))) {
                return asset('storage/'.$this->foto);
            }

            if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
                return $this->foto;
            }

            if (file_exists(public_path($this->foto))) {
                return asset($this->foto);
            }
        }

        return null;
    }

    /**
     * Hapus file foto dari disk 'public' saat produk di-force delete.
     * Saat soft delete, foto dibiarkan agar aman bila produk di-restore.
     */
    protected static function booted(): void
    {
        static::forceDeleting(function (Product $product): void {
            if ($product->foto) {
                Storage::disk('public')->delete($product->foto);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok' => 'integer',
            'max_per_order' => 'integer',
            'status_stok' => StatusStok::class,
        ];
    }

    /**
     * Kategori dari produk ini.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Baris item pesanan yang memesan produk ini.
     *
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Pesanan yang memuat produk ini (relasi many-to-many via order_items).
     *
     * @return BelongsToMany<Order, $this>
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['jumlah', 'subtotal', 'catatan', 'deskripsi_custom', 'foto_referensi', 'ukuran_kue', 'rasa_kue'])
            ->withTimestamps();
    }
}
