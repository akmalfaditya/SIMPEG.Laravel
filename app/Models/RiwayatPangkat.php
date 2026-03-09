<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RiwayatPangkat extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'pegawai_id',
        'golongan_id',
        'nomor_sk',
        'tmt_pangkat',
        'tanggal_sk',
        'file_pdf_path',
        'google_drive_link',
        'is_hukdis_demotion',
    ];

    protected $casts = [
        'tmt_pangkat' => 'date',
        'tanggal_sk' => 'date',
        'is_hukdis_demotion' => 'boolean',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function golongan(): BelongsTo
    {
        return $this->belongsTo(GolonganPangkat::class, 'golongan_id');
    }
}
