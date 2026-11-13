<?php

namespace App\Enums;

enum StatusPembayaran: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Expired = 'expired';
    case Failed = 'failed';

    /**
     * Label status pembayaran untuk ditampilkan ke pembeli/admin.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::Paid => 'Lunas',
            self::Expired => 'Kedaluwarsa',
            self::Failed => 'Gagal',
        };
    }
}
