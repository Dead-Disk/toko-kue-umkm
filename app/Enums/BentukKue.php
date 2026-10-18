<?php

namespace App\Enums;

enum BentukKue: string
{
    case Lingkaran = 'lingkaran';
    case Kotak = 'kotak';
    case Custom = 'custom';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Lingkaran => 'Lingkaran',
            self::Kotak => 'Kotak',
            self::Custom => 'Custom',
            self::Lainnya => 'Lainnya',
        };
    }
}
