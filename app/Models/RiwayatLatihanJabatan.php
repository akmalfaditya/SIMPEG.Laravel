<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RiwayatLatihanJabatan extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs();
    }
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
