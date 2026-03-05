<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatLatihanJabatan extends Model
{
    protected $fillable = [
        'pegawai_id', 'nama_latihan', 'tahun_pelaksanaan', 'jumlah_jam',
        'penyelenggara', 'tempat_pelaksanaan', 'no_sertifikat',
        'file_pdf_path', 'google_drive_link',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
