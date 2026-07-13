<?php

namespace App\Enums;

enum StatusKepegawaianTendik: string
{
    case PNS = 'PNS';
    case PTTT = 'PTTT';
    case HONORER = 'Honorer';
    case LAINNYA = 'Lainnya';

    public function label(): string
    {
        return match ($this) {
            self::PNS => 'PNS',
            self::PTTT => 'PTTT',
            self::HONORER => 'Honorer',
            self::LAINNYA => 'Lainnya',
        };
    }
}
