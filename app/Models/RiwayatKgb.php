<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKgb extends Model
{
    protected $fillable = [
        'pegawai_id', 'nomor_sk', 'tmt_kgb',
        'gaji_lama', 'gaji_baru',
        'masa_kerja_golongan_tahun', 'masa_kerja_golongan_bulan',
        'file_pdf_path', 'google_drive_link',
    ];

    protected $casts = [
        'tmt_kgb' => 'date',
        'gaji_lama' => 'decimal:2',
        'gaji_baru' => 'decimal:2',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
