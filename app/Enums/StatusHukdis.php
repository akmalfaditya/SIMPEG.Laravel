<?php

namespace App\Enums;

enum StatusHukdis: string
{
    case Aktif = 'aktif';
    case Selesai = 'selesai';
    case Dipulihkan = 'dipulihkan';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Selesai => 'Selesai',
            self::Dipulihkan => 'Dipulihkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Aktif => 'bg-red-100 text-red-700',
            self::Selesai => 'bg-slate-100 text-slate-700',
            self::Dipulihkan => 'bg-green-100 text-green-700',
        };
    }
}
