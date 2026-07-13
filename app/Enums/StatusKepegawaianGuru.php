<?php

namespace App\Enums;

enum StatusKepegawaianGuru: string
{
    case PNS = 'PNS';
    case GTY = 'GTY';
    case HONORER = 'Honorer';
    case LAINNYA = 'Lainnya';

    public function label(): string
    {
        return match ($this) {
            self::PNS => 'PNS',
            self::GTY => 'GTY',
            self::HONORER => 'Honorer',
            self::LAINNYA => 'Lainnya',
        };
    }
}
