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

#[Fillable([
    'category_id',
    'nama_kue',
    'slug',
    'deskripsi',
    'harga',
    'stok',
    'status_stok',
    'foto',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

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
            ->withPivot(['jumlah', 'subtotal', 'deskripsi_custom', 'foto_referensi', 'ukuran_kue', 'rasa_kue'])
            ->withTimestamps();
    }
}
