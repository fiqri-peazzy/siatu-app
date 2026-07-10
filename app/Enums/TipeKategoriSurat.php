<?php

namespace App\Enums;

enum TipeKategoriSurat: string
{
    case MASUK = 'masuk';
    case KELUAR = 'keluar';
    case KEDUANYA = 'keduanya';

    public function label(): string
    {
        return match($this) {
            self::MASUK => 'Surat Masuk',
            self::KELUAR => 'Surat Keluar',
            self::KEDUANYA => 'Masuk & Keluar',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
