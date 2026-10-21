<?php

namespace App\Models;

use App\Enums\MetodePembayaran;
use Database\Factories\StoreSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'store_name',
    'store_description',
    'store_address',
    'store_phone',
    'bank_name',
    'bank_account_number',
    'bank_account_holder',
    'qris_image_path',
])]
class StoreSetting extends Model
{
    /** @use HasFactory<StoreSettingFactory> */
    use HasFactory;

    /**
     * Pengaturan toko bersifat single-row. Baris kosong belum tentu ada di
     * database, jadi pemanggil tetap mendapat instance yang bisa diisi.
     */
    public static function current(): self
    {
        return static::query()->firstOrNew([]);
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
