<?php

namespace App\Enums;

enum TingkatHukuman: int
{
    case Ringan = 1;
    case Sedang = 2;
    case Berat = 3;

    public function label(): string
    {
        return $this->name;
    }
}
