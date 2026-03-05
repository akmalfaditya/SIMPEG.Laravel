<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPenghargaan extends Model
{
    protected $fillable = [
        'pegawai_id', 'nama_penghargaan', 'tahun', 'milestone',
        'nomor_sk', 'tanggal_sk', 'file_pdf_path', 'google_drive_link',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
