<?php

namespace App\Enums;

enum GolonganDarah: int
{
    case A = 1;
    case B = 2;
    case AB = 3;
    case O = 4;

    public function label(): string
    {
        return $this->name;
    }
}
