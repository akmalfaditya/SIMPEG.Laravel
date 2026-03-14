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
        $nama = $this->pegawai->nama_lengkap ?? 'Unknown';
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Riwayat Pangkat untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'updated' => "Mengubah Riwayat Pangkat untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'deleted' => "Menghapus Riwayat Pangkat untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                default   => "{$eventName} Riwayat Pangkat untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
            });
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
