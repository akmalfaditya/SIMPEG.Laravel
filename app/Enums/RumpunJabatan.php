<?php

namespace App\Enums;

enum RumpunJabatan: int
{
    case Imigrasi = 1;
    case Pemasyarakatan = 2;
    case Struktural = 3;

    public function label(): string
    {
        return match ($this) {
            self::Imigrasi => 'Imigrasi',
            self::Pemasyarakatan => 'Pemasyarakatan',
            self::Struktural => 'Struktural',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Imigrasi => 'blue',
            self::Pemasyarakatan => 'amber',
            self::Struktural => 'slate',
        };
    }
}
