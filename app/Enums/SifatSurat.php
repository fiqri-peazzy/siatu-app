<?php

namespace App\Enums;

enum SifatSurat: string
{
    case BIASA = 'biasa';
    case PENTING = 'penting';
    case SEGERA = 'segera';
    case RAHASIA = 'rahasia';

    public function label(): string
    {
        return match($this) {
            self::BIASA => 'Biasa',
            self::PENTING => 'Penting',
            self::SEGERA => 'Segera',
            self::RAHASIA => 'Rahasia',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
