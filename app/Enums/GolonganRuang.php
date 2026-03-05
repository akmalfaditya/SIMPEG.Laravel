<?php

namespace App\Enums;

enum GolonganRuang: int
{
    case I_a = 1;
    case I_b = 2;
    case I_c = 3;
    case I_d = 4;
    case II_a = 5;
    case II_b = 6;
    case II_c = 7;
    case II_d = 8;
    case III_a = 9;
    case III_b = 10;
    case III_c = 11;
    case III_d = 12;
    case IV_a = 13;
    case IV_b = 14;
    case IV_c = 15;
    case IV_d = 16;
    case IV_e = 17;

    public function label(): string
    {
        return match ($this) {
            self::I_a => 'I/a',
            self::I_b => 'I/b',
            self::I_c => 'I/c',
            self::I_d => 'I/d',
            self::II_a => 'II/a',
            self::II_b => 'II/b',
            self::II_c => 'II/c',
            self::II_d => 'II/d',
            self::III_a => 'III/a',
            self::III_b => 'III/b',
            self::III_c => 'III/c',
            self::III_d => 'III/d',
            self::IV_a => 'IV/a',
            self::IV_b => 'IV/b',
            self::IV_c => 'IV/c',
            self::IV_d => 'IV/d',
            self::IV_e => 'IV/e',
        };
    }
}
