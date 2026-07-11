<?php

namespace App\Enums;

enum StatusAbsensiSiswa: string
{
    case HADIR = 'hadir';
    case IZIN = 'izin';
    case SAKIT = 'sakit';
    case ALFA = 'alfa';

    public function label(): string
    {
        return match($this) {
            self::HADIR => 'Hadir',
            self::IZIN => 'Izin',
            self::SAKIT => 'Sakit',
            self::ALFA => 'Alfa',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
