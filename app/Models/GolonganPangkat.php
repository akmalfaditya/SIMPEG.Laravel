<?php

namespace App\Models;

use App\Enums\GolonganRuang;
use Illuminate\Database\Eloquent\Model;

class GolonganPangkat extends Model
{
    protected $fillable = [
        'golongan_ruang',
        'label',
        'pangkat',
        'golongan_group',
        'min_pendidikan',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'golongan_ruang' => GolonganRuang::class,
        'is_active' => 'boolean',
    ];
}
