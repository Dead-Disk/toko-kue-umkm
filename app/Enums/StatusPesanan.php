<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case Pending = 'pending';
    case Diproses = 'diproses';
    case Selesai = 'selesai';
    case Batal = 'batal';
}
