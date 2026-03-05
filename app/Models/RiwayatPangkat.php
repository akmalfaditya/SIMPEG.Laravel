<?php

namespace App\Models;

use App\Enums\GolonganRuang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPangkat extends Model
{
    protected $fillable = [
        'pegawai_id', 'golongan_ruang', 'nomor_sk',
        'tmt_pangkat', 'tanggal_sk', 'file_pdf_path', 'google_drive_link',
    ];

    protected $casts = [
        'golongan_ruang' => GolonganRuang::class,
        'tmt_pangkat' => 'date',
        'tanggal_sk' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
