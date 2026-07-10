<?php

namespace App\Enums;

enum StatusSuratMasuk: string
{
    case BARU = 'baru';
    case DIDISPOSISI = 'didisposisi';
    case DIPROSES = 'diproses';
    case SELESAI = 'selesai';
    case DIARSIPKAN = 'diarsipkan';

    public function label(): string
    {
        return match($this) {
            self::BARU => 'Baru',
            self::DIDISPOSISI => 'Didisposisi',
            self::DIPROSES => 'Diproses',
            self::SELESAI => 'Selesai',
            self::DIARSIPKAN => 'Diarsipkan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
