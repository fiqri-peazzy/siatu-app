<?php

namespace App\Enums;

enum StatusPersonil: string
{
    case AKTIF = 'aktif';
    case NONAKTIF = 'nonaktif';
    case PENSIUN = 'pensiun';
    case MUTASI = 'mutasi';

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::NONAKTIF => 'Nonaktif',
            self::PENSIUN => 'Pensiun',
            self::MUTASI => 'Mutasi',
        };
    }
}
