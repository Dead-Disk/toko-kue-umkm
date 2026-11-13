<?php

namespace App\Enums;

enum OpsiPengiriman: string
{
    case KurirToko = 'kurir_toko';
    case AmbilSendiri = 'ambil_sendiri';

    /**
     * Label opsi pengiriman untuk ditampilkan ke pembeli/admin.
     */
    public function label(): string
    {
        return match ($this) {
            self::KurirToko => 'Diantar Kurir',
            self::AmbilSendiri => 'Ambil Sendiri',
        };
    }
}
