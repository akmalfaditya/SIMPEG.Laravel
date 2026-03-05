<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPendidikan extends Model
{
    protected $fillable = [
        'pegawai_id', 'tingkat_pendidikan', 'institusi', 'jurusan',
        'tahun_lulus', 'no_ijazah', 'tanggal_ijazah',
        'file_pdf_path', 'google_drive_link',
    ];

    protected $casts = [
        'tanggal_ijazah' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
