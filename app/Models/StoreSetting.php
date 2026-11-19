<?php

namespace App\Models;

use App\Enums\MetodePembayaran;
use Database\Factories\StoreSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'store_name',
    'store_description',
    'store_address',
    'store_phone',
    'logo_path',
    'banner_path',
    'store_email',
    'instagram_url',
    'facebook_url',
    'tiktok_url',
    'bank_name',
    'bank_account_number',
    'bank_account_holder',
    'qris_image_path',
    'banner_scale',
    'banner_size',
    'banner_slide_duration',
    'banner_auto_slide',
])]
class StoreSetting extends Model
{
    /** @use HasFactory<StoreSettingFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'banner_slide_duration' => 'integer',
            'banner_auto_slide' => 'boolean',
        ];
    }

    /**
     * Class CSS Tailwind untuk aspek rasio wadah banner.
     */
    public function bannerAspectRatioClass(): string
    {
        return match ($this->banner_size ?? '21:9') {
            '16:9' => 'aspect-[16/9]',
            '16:7' => 'aspect-[16/7]',
            '4:1' => 'aspect-[4/1]',
            default => 'aspect-[21/9]',
        };
    }

    /**
     * Class CSS Tailwind untuk object fit gambar banner.
     */
    public function bannerScaleClass(?string $itemScale = null): string
    {
        $scale = $itemScale ?: ($this->banner_scale ?? 'cover');

        return match ($scale) {
            'contain' => 'object-contain',
            'fill' => 'object-fill',
            default => 'object-cover',
        };
    }

    /**
     * Pengaturan toko bersifat single-row. Baris kosong belum tentu ada di
     * database, jadi pemanggil tetap mendapat instance yang bisa diisi.
     */
    public static function current(): self
    {
        return static::query()->firstOrNew([]);
    }

    /**
     * Logo toko dianggap siap bila admin sudah mengunggahnya.
     */
    public function hasLogo(): bool
    {
        return filled($this->logo_path);
    }

    /**
     * Banner toko dianggap siap bila sudah diunggah di pengaturan toko.
     */
    public function hasBanner(): bool
    {
        return filled($this->banner_path);
    }

    /**
     * URL banner toko menggunakan path relatif (/storage/...) agar aman diakses via LAN/mobile.
     */
    public function bannerUrl(): ?string
    {
        if (! $this->hasBanner()) {
            return null;
        }

        return Banner::resolveImageUrl($this->banner_path);
    }

    /**
     * URL logo toko dengan fallback ke logo bawaan aplikasi.
     */
    public function logoUrl(): string
    {
        return $this->hasLogo()
            ? asset('storage/'.$this->logo_path)
            : asset('images/logo-nav.png');
    }

    /**
     * Rekening bank dianggap siap bila ketiga data inti terisi.
     */
    public function hasBankDetails(): bool
    {
        return filled($this->bank_name)
            && filled($this->bank_account_number)
            && filled($this->bank_account_holder);
    }

    /**
     * QRIS dianggap siap bila gambar sudah diunggah admin.
     */
    public function hasQrisImage(): bool
    {
        return filled($this->qris_image_path);
    }

    /**
     * Apakah metode pembayaran tertentu sudah bisa dipakai pembeli.
     */
    public function supports(MetodePembayaran $metode): bool
    {
        return match ($metode) {
            MetodePembayaran::TransferBank => $this->hasBankDetails(),
            MetodePembayaran::Qris => $this->hasQrisImage(),
            MetodePembayaran::Cash => true,
        };
    }
}
