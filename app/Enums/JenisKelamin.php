<?php

namespace App\Enums;

enum JenisKelamin: int
{
    case LakiLaki = 1;
    case Perempuan = 2;

    public function label(): string
    {
        return match ($this) {
            self::LakiLaki => 'Laki-Laki',
            self::Perempuan => 'Perempuan',
        };
    }
}
