<?php

namespace App\Enums;

enum MetodePembayaran: string
{
    case TransferBank = 'transfer_bank';
    case Qris = 'qris';
}
