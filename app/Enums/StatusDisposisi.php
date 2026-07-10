<?php

namespace App\Enums;

enum StatusDisposisi: string
{
    case DITERIMA = 'diterima';
    case DIPROSES = 'diproses';
    case SELESAI = 'selesai';
    case DITERUSKAN = 'diteruskan';

    public function label(): string
    {
        return match($this) {
            self::DITERIMA => 'Diterima',
            self::DIPROSES => 'Diproses',
            self::SELESAI => 'Selesai',
            self::DITERUSKAN => 'Diteruskan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
