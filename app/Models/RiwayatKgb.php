<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RiwayatKgb extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $nama = $this->pegawai->nama_lengkap ?? 'Unknown';
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Riwayat KGB untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'updated' => "Mengubah Riwayat KGB untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'deleted' => "Menghapus Riwayat KGB untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                default   => "{$eventName} Riwayat KGB untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
            });
    }
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
