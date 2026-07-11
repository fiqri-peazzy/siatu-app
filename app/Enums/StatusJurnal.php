<?php

namespace App\Enums;

enum StatusJurnal: string
{
    case BELUM_DIISI = 'belum_diisi';
    case DIISI = 'diisi';

    public function label(): string
    {
        return match($this) {
            self::BELUM_DIISI => 'Belum Diisi',
            self::DIISI => 'Sudah Diisi',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
