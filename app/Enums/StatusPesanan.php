<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case Pending = 'pending';
    case Proses = 'proses';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Batal = 'batal';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Proses => 'Diproses',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
            self::Batal => 'Dibatalkan',
            self::Expired => 'Kedaluwarsa',
        };
    }

    /**
     * Apakah status ini termasuk status final yang melepaskan kunci sesi aktif custom order.
     */
    public function isFinalForCustomLock(): bool
    {
        return in_array($this, [
            self::Proses,
            self::Selesai,
            self::Ditolak,
            self::Batal,
            self::Expired,
        ], true);
    }
}
