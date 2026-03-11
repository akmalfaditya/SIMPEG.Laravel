<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RiwayatPenghargaan extends Model
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
                'created' => "Menambah Riwayat Penghargaan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'updated' => "Mengubah Riwayat Penghargaan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'deleted' => "Menghapus Riwayat Penghargaan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                default   => "{$eventName} Riwayat Penghargaan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
            });
    }
    protected $fillable = [
        'pegawai_id', 'nama_penghargaan', 'tahun', 'milestone',
        'nomor_sk', 'tanggal_sk', 'file_pdf_path', 'google_drive_link',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
