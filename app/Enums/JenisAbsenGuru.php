<?php

namespace App\Enums;

enum JenisAbsenGuru: string
{
    case MASUK = 'masuk';
    case PULANG = 'pulang';

    public function label(): string
    {
        return match($this) {
            self::MASUK => 'Absen Masuk',
            self::PULANG => 'Absen Pulang',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
