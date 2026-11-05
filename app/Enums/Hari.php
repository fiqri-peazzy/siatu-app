<?php

namespace App\Enums;

enum Hari: string
{
    case SENIN = 'senin';
    case SELASA = 'selasa';
    case RABU = 'rabu';
    case KAMIS = 'kamis';
    case JUMAT = 'jumat';
    case SABTU = 'sabtu';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** ISO-8601 day number (1 = Senin) — cocok dengan Carbon::dayOfWeekIso */
    public function iso(): int
    {
        return match ($this) {
            self::SENIN => 1,
            self::SELASA => 2,
            self::RABU => 3,
            self::KAMIS => 4,
            self::JUMAT => 5,
            self::SABTU => 6,
        };
    }

    public static function fromIso(int $iso): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->iso() === $iso) {
                return $case;
            }
        }

        return null;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
