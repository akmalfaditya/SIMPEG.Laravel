<?php

namespace App\Models;

use App\Enums\JenisSanksi;
use App\Enums\StatusHukdis;
use App\Enums\TingkatHukuman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RiwayatHukumanDisiplin extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'pegawai_id', 'tingkat_hukuman', 'jenis_sanksi',
        'durasi_tahun', 'nomor_sk', 'tanggal_sk', 'tmt_hukuman',
        'tmt_selesai_hukuman', 'deskripsi',
        'file_pdf_path', 'google_drive_link',
        'status', 'nomor_sk_pemulihan', 'tanggal_pemulihan', 'file_sk_pemulihan_path',
    ];

    protected $casts = [
        'tingkat_hukuman' => TingkatHukuman::class,
        'jenis_sanksi' => JenisSanksi::class,
        'status' => StatusHukdis::class,
        'tanggal_sk' => 'date',
        'tmt_hukuman' => 'date',
        'tmt_selesai_hukuman' => 'date',
        'tanggal_pemulihan' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function isAktif(): bool
    {
        return $this->status === StatusHukdis::Aktif
            && ($this->tmt_selesai_hukuman === null || $this->tmt_selesai_hukuman->gte(today()));
    }

    public function isType2(): bool
    {
        return in_array($this->jenis_sanksi, [
            JenisSanksi::PenurunanPangkat,
            JenisSanksi::PenurunanJabatan,
            JenisSanksi::PembebasanJabatan,
        ]);
    }
}
