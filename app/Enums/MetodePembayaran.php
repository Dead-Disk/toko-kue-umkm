<?php

namespace App\Enums;

enum MetodePembayaran: string
{
    case TransferBank = 'transfer_bank';
    case Qris = 'qris';
    case Cash = 'cash';

    /**
     * Label metode pembayaran untuk ditampilkan ke pembeli/admin.
     */
    public function label(): string
    {
        return match ($this) {
            self::TransferBank => 'Transfer Bank',
            self::Qris => 'QRIS',
            self::Cash => 'Tunai / Bayar di Toko',
        };
    }
}
