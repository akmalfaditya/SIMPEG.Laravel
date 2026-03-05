<?php

namespace App\Enums;

enum JenisJabatan: int
{
    case PejabatAdministrasi = 1;
    case FungsionalAhliPertama = 2;
    case FungsionalAhliMuda = 3;
    case PejabatPimpinanTinggi = 4;
    case FungsionalMadya = 5;
    case FungsionalUtama = 6;

    public function label(): string
    {
        return match ($this) {
            self::PejabatAdministrasi => 'Pejabat Administrasi',
            self::FungsionalAhliPertama => 'Fungsional Ahli Pertama',
            self::FungsionalAhliMuda => 'Fungsional Ahli Muda',
            self::PejabatPimpinanTinggi => 'Pejabat Pimpinan Tinggi',
            self::FungsionalMadya => 'Fungsional Madya',
            self::FungsionalUtama => 'Fungsional Utama',
        };
    }
}
