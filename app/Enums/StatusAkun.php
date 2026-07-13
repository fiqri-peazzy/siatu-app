<?php

namespace App\Enums;

enum StatusAkun: string
{
    case BELUM_ADA = 'belum_ada';
    case PENDING = 'pending';
    case AKTIF = 'aktif';

    public function label(): string
    {
        return match ($this) {
            self::BELUM_ADA => 'Belum Ada',
            self::PENDING => 'Pending',
            self::AKTIF => 'Aktif',
        };
    }
}
