<?php

namespace App\Models;

use App\Enums\GolonganRuang;
use Illuminate\Database\Eloquent\Model;

class TabelGaji extends Model
{
    protected $fillable = [
        'golongan_ruang',
        'masa_kerja_tahun',
        'gaji_pokok',
    ];

    protected $casts = [
        'golongan_ruang' => GolonganRuang::class,
        'gaji_pokok' => 'decimal:2',
    ];
}
