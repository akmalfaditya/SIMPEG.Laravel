<?php

namespace App\Models;

use App\Enums\TingkatHukuman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatHukumanDisiplin extends Model
{
    protected $fillable = [
        'pegawai_id', 'tingkat_hukuman', 'jenis_hukuman',
        'nomor_sk', 'tanggal_sk', 'tmt_hukuman',
        'tmt_selesai_hukuman', 'deskripsi',
    ];

    protected $casts = [
        'tingkat_hukuman' => TingkatHukuman::class,
        'tanggal_sk' => 'date',
        'tmt_hukuman' => 'date',
        'tmt_selesai_hukuman' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
