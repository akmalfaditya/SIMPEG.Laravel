<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatJabatan extends Model
{
    protected $fillable = [
        'pegawai_id', 'jabatan_id', 'nomor_sk',
        'tmt_jabatan', 'tanggal_sk', 'file_pdf_path', 'google_drive_link',
    ];

    protected $casts = [
        'tmt_jabatan' => 'date',
        'tanggal_sk' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }
}
