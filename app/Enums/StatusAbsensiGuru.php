<?php

namespace App\Enums;

enum StatusAbsensiGuru: string
{
    case HADIR = 'hadir';
    case IZIN = 'izin';
    case SAKIT = 'sakit';
    case ALFA = 'alfa';
    case DINAS_LUAR = 'dinas_luar';
    case TERLAMBAT = 'terlambat';

    public function label(): string
    {
        return match($this) {
            self::HADIR => 'Hadir',
            self::IZIN => 'Izin',
            self::SAKIT => 'Sakit',
            self::ALFA => 'Alfa',
            self::DINAS_LUAR => 'Dinas Luar',
            self::TERLAMBAT => 'Terlambat',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
