<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
    protected $appends = ['name', 'foto_url', 'svg_url'];

    /**
     * Alias "name" untuk kolom nama_kategori (kompatibilitas penamaan umum).
     */
    public function getNameAttribute(): ?string
    {
        return $this->nama_kategori;
    }

    /**
     * URL ilustrasi SVG representatif kategori dari /assets/kategori.
     */
    public function getFotoUrlAttribute(): string
    {
        return $this->svg_url;
    }

    /**
     * URL SVG kategori dari direktori /assets/kategori.
     */
    public function getSvgUrlAttribute(): string
    {
        $slug = $this->slug ?? Str::slug($this->nama_kategori ?? '');

        return asset('assets/kategori/'.self::resolveSvgFilename($slug));
    }

    /**
     * Menyelesaikan nama file SVG berdasarkan slug atau nama kategori.
     */
    public static function resolveSvgFilename(?string $slug): string
    {
        if (empty($slug)) {
            return 'default.svg';
        }

        $slug = Str::slug($slug);

        if (file_exists(public_path('assets/kategori/'.$slug.'.svg'))) {
            return $slug.'.svg';
        }

        if (str_contains($slug, 'bento') || str_contains($slug, 'lunchbox')) {
            return 'bento.svg';
        }

        if (str_contains($slug, 'tart') || str_contains($slug, 'cake') || str_contains($slug, 'ulang-tahun') || str_contains($slug, 'birthday') || str_contains($slug, 'black-forest')) {
            return 'kue-tart.svg';
        }

        if (str_contains($slug, 'donat') || str_contains($slug, 'donut')) {
            return 'donat.svg';
        }

        if (str_contains($slug, 'gulung') || str_contains($slug, 'roll') || str_contains($slug, 'bolu')) {
            return 'roti-gulung.svg';
        }

        if (str_contains($slug, 'spiku') || str_contains($slug, 'lapis') || str_contains($slug, 'surabaya')) {
            return 'spiku.svg';
        }

        if (str_contains($slug, 'kering') || str_contains($slug, 'cookie') || str_contains($slug, 'nastar') || str_contains($slug, 'kastengel') || str_contains($slug, 'semprit')) {
            return 'kue-kering.svg';
        }

        if (str_contains($slug, 'roti') || str_contains($slug, 'bread') || str_contains($slug, 'bakery') || str_contains($slug, 'croissant') || str_contains($slug, 'pastry')) {
            return 'roti.svg';
        }

        return 'default.svg';
    }

    /**
     * Cek apakah kategori memiliki foto/ikon representatif.
     */
    public function hasFoto(): bool
    {
        return true;
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
