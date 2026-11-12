<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case Pending = 'pending';
    case Proses = 'proses';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Batal = 'batal';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Proses => 'Diproses',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
            self::Batal => 'Dibatalkan',
        };
    }
}
