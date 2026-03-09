<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active' => 'boolean',
    ];

    public function riwayatPangkat(): HasMany
    {
        return $this->hasMany(RiwayatPangkat::class, 'golongan_id');
    }

    public function tabelGaji(): HasMany
    {
        return $this->hasMany(TabelGaji::class, 'golongan_id');
    }
}
