<?php

namespace App\Enums;

enum JenisSanksi: int
{
    case PenundaanKgb = 1;
    case PenundaanPangkat = 2;
    case PenurunanPangkat = 3;
    case PenurunanJabatan = 4;
    case PembebasanJabatan = 5;
    case Pemberhentian = 6;

    public function label(): string
    {
        return match ($this) {
            self::PenundaanKgb => 'Penundaan KGB',
            self::PenundaanPangkat => 'Penundaan Pangkat',
            self::PenurunanPangkat => 'Penurunan Pangkat',
            self::PenurunanJabatan => 'Penurunan Jabatan',
            self::PembebasanJabatan => 'Pembebasan Jabatan',
            self::Pemberhentian => 'Pemberhentian',
        };
    }
}
