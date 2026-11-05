<?php

namespace App\Enums;

enum JenisSlotJam: string
{
    case PELAJARAN = 'pelajaran';
    case ISTIRAHAT = 'istirahat';
    case UPACARA = 'upacara';

    public function label(): string
    {
        return match ($this) {
            self::PELAJARAN => 'Jam Pelajaran',
            self::ISTIRAHAT => 'Istirahat',
            self::UPACARA => 'Upacara',
        };
    }

    /** Slot yang bisa diisi sesi (mapel/kegiatan) */
    public function bisaDiisi(): bool
    {
        return $this === self::PELAJARAN;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
