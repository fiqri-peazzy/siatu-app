<?php

namespace App\Enums;

enum JabatanTendik: string
{
    case KEPALA_TU = 'Kepala TU';
    case STAF_TU = 'Staf TU';
    case PUSTAKAWAN = 'Pustakawan';
    case SATPAM = 'Satpam';
    case PENJAGA_SEKOLAH = 'Penjaga Sekolah';
    case LABORAN = 'Laboran';
    case OPERATOR = 'Operator';
    case LAINNYA = 'Lainnya';

    public function label(): string
    {
        return match ($this) {
            self::KEPALA_TU => 'Kepala TU',
            self::STAF_TU => 'Staf TU',
            self::PUSTAKAWAN => 'Pustakawan',
            self::SATPAM => 'Satpam',
            self::PENJAGA_SEKOLAH => 'Penjaga Sekolah',
            self::LABORAN => 'Laboran',
            self::OPERATOR => 'Operator',
            self::LAINNYA => 'Lainnya',
        };
    }
}
