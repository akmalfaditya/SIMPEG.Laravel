<?php

namespace App\Models;

use App\Enums\GolonganRuang;
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
        'pegawai_id', 'golongan_ruang', 'nomor_sk',
        'tmt_pangkat', 'tanggal_sk', 'file_pdf_path', 'google_drive_link',
        'is_hukdis_demotion',
    ];

    protected $casts = [
        'golongan_ruang' => GolonganRuang::class,
        'tmt_pangkat' => 'date',
        'tanggal_sk' => 'date',
        'is_hukdis_demotion' => 'boolean',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
