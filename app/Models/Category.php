<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama_kategori', 'slug'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['name', 'foto_url'];

    /**
     * Alias "name" untuk kolom nama_kategori (kompatibilitas penamaan umum).
     */
    public function getNameAttribute(): ?string
    {
        return $this->nama_kategori;
    }

    /**
     * Foto representatif kategori yang diambil dari foto produk pertama yang valid dalam kategori ini.
     */
    public function getFotoUrlAttribute(): ?string
    {
        $productWithPhoto = $this->relationLoaded('products')
            ? $this->products->first(fn (Product $p): bool => $p->hasRealFoto())
            : $this->products()->whereNotNull('foto')->get()->first(fn (Product $p): bool => $p->hasRealFoto());

        return $productWithPhoto?->foto_url;
    }

    /**
     * Cek apakah kategori memiliki foto produk yang valid.
     */
    public function hasFoto(): bool
    {
        return ! empty($this->foto_url);
    }

    /**
     * Produk-produk yang termasuk ke dalam kategori ini.
     *
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
