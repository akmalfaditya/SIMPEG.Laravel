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
        $nama = $this->pegawai->nama_lengkap ?? 'Unknown';
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Riwayat Latihan Jabatan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'updated' => "Mengubah Riwayat Latihan Jabatan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'deleted' => "Menghapus Riwayat Latihan Jabatan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                default   => "{$eventName} Riwayat Latihan Jabatan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
            });
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
