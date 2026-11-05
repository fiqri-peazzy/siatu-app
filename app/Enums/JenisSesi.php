<?php

namespace App\Enums;

enum JenisSesi: string
{
    /** Mapel reguler untuk satu/lebih rombel */
    case PEMBELAJARAN = 'pembelajaran';
    /** Mapel pilihan paralel — peserta dari peminatan_siswas */
    case PILIHAN = 'pilihan';
    /** Tahfidz, Pembinaan ORTOM, ekstrakurikuler, dsb. */
    case KEGIATAN = 'kegiatan';

    public function label(): string
    {
        return match ($this) {
            self::PEMBELAJARAN => 'Pembelajaran',
            self::PILIHAN => 'Mapel Pilihan',
            self::KEGIATAN => 'Kegiatan',
        };
    }

    /** Sesi yang wajib diisi jurnal oleh guru */
    public function wajibJurnal(): bool
    {
        return $this !== self::KEGIATAN;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
