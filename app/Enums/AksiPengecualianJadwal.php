<?php

namespace App\Enums;

enum AksiPengecualianJadwal: string
{
    case TIADAKAN = 'tiadakan';
    case UBAH_WAKTU = 'ubah_waktu';
    case TAMBAH = 'tambah';

    public function label(): string
    {
        return match ($this) {
            self::TIADAKAN => 'Ditiadakan',
            self::UBAH_WAKTU => 'Ubah Waktu',
            self::TAMBAH => 'Kegiatan Sisipan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
