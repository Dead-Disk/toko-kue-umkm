<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case Pending = 'pending';
    case Proses = 'proses';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Batal = 'batal';
}
