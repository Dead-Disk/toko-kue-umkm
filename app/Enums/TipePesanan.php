<?php

namespace App\Enums;

enum TipePesanan: string
{
    case Reguler = 'reguler';
    case Custom = 'custom';

    /**
     * Label tipe pesanan untuk ditampilkan ke pembeli/admin.
     */
    public function label(): string
    {
        return match ($this) {
            self::Reguler => 'Kue Reguler',
            self::Custom => 'Custom Cake',
        };
    }

    /**
     * Ambil label aman dari instance enum, string mentah, atau null.
     */
    public static function labelFor(self|string|null $value): string
    {
        if ($value instanceof self) {
            return $value->label();
        }

        if (is_string($value)) {
            $resolved = self::tryFrom(strtolower(trim($value)));
            if ($resolved !== null) {
                return $resolved->label();
            }

            if ($value !== '') {
                return ucfirst($value);
            }
        }

        return '-';
    }
}
