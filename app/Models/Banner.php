<?php

namespace App\Models;

use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'judul',
    'subjudul',
    'gambar',
    'link',
    'badge',
    'scale',
    'urutan',
    'is_active',
])]
class Banner extends Model
{
    /** @use HasFactory<BannerFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * URL publik untuk file gambar banner menggunakan path relatif (/storage/...)
     * agar aman diakses dari perangkat lain melalui IP LAN tanpa hardcoded domain.
     */
    public function getGambarUrlAttribute(): string
    {
        return self::resolveImageUrl($this->gambar);
    }

    /**
     * Menyelesaikan URL atau path gambar banner menjadi path relatif (/storage/...)
     * atau mempertahankan URL eksternal jika menggunakan http(s).
     */
    public static function resolveImageUrl(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        $path = trim($path);

        // Jika merupakan URL penuh lokal/storage (misal: http://127.0.0.1:8000/storage/...)
        if (preg_match('#^https?://[^/]+(/.*)$#i', $path, $matches)) {
            $urlPath = $matches[1];
            if (str_contains($urlPath, '/storage/')) {
                $storagePart = preg_replace('#^.*?(/storage/(?:storage/)*)#', '', $urlPath);

                return '/storage/'.ltrim($storagePart, '/');
            }

            return $path;
        }

        // Jika merupakan URL eksternal murni
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Jika menunjuk ke folder public bawaan (images/...)
        if (preg_match('#^/?images/#', $path)) {
            return '/'.ltrim($path, '/');
        }

        // Tangani prefix berulang seperti public/, storage/, /storage/
        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'public/')) {
            $cleanPath = substr($cleanPath, 7);
        }
        while (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        $cleanPath = ltrim($cleanPath, '/');

        return '/storage/'.$cleanPath;
    }
}
