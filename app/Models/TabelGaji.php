<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TabelGaji extends Model
{
    protected $fillable = [
        'golongan_id',
        'masa_kerja_tahun',
        'gaji_pokok',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
    ];

    public function golongan(): BelongsTo
    {
        return $this->belongsTo(GolonganPangkat::class, 'golongan_id');
    }
}
