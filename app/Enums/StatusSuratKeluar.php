<?php

namespace App\Enums;

enum StatusSuratKeluar: string
{
    case DRAFT = 'draft';
    case MENUNGGU_APPROVAL = 'menunggu_approval';
    case APPROVED = 'approved';
    case REVISI = 'revisi';
    case TERKIRIM = 'terkirim';
    case DIARSIPKAN = 'diarsipkan';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::MENUNGGU_APPROVAL => 'Menunggu Approval',
            self::APPROVED => 'Approved',
            self::REVISI => 'Revisi',
            self::TERKIRIM => 'Terkirim',
            self::DIARSIPKAN => 'Diarsipkan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
