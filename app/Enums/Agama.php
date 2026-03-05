<?php

namespace App\Enums;

enum Agama: int
{
    case Islam = 1;
    case Kristen = 2;
    case Katolik = 3;
    case Hindu = 4;
    case Budha = 5;
    case Konghucu = 6;

    public function label(): string
    {
        return $this->name;
    }
}
