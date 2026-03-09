<?php

namespace App\Models;

use App\Enums\JenisSanksi;
use App\Enums\TingkatHukuman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatHukumanDisiplin extends Model
{
    protected $fillable = [
        'pegawai_id', 'tingkat_hukuman', 'jenis_sanksi',
        'durasi_tahun', 'nomor_sk', 'tanggal_sk', 'tmt_hukuman',
        'tmt_selesai_hukuman', 'deskripsi',
        'file_pdf_path', 'google_drive_link',
    ];

    protected $casts = [
        'tingkat_hukuman' => TingkatHukuman::class,
        'jenis_sanksi' => JenisSanksi::class,
        'tanggal_sk' => 'date',
        'tmt_hukuman' => 'date',
        'tmt_selesai_hukuman' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
