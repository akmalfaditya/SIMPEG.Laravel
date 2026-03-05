<?php

namespace App\Models;

use App\Enums\JenisJabatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    protected $fillable = [
        'nama_jabatan', 'jenis_jabatan', 'bup', 'eselon_level', 'kelas_jabatan',
    ];

    protected $casts = [
        'jenis_jabatan' => JenisJabatan::class,
    ];

    public function riwayatJabatan(): HasMany
    {
        return $this->hasMany(RiwayatJabatan::class);
    }
}
