<?php

namespace App\Enums;

enum KategoriAbsensi: string
{
    case GURU = 'guru';
    case TENDIK = 'tendik';

    public function label(): string
    {
        return match ($this) {
            self::GURU => 'Guru',
            self::TENDIK => 'Tenaga Kependidikan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
