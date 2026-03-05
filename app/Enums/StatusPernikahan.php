<?php

namespace App\Enums;

enum StatusPernikahan: int
{
    case BelumMenikah = 1;
    case Menikah = 2;
    case CeraiHidup = 3;
    case CeraiMati = 4;

    public function label(): string
    {
        return match ($this) {
            self::BelumMenikah => 'Belum Menikah',
            self::Menikah => 'Menikah',
            self::CeraiHidup => 'Cerai Hidup',
            self::CeraiMati => 'Cerai Mati',
        };
    }
}
