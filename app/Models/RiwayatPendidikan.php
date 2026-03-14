<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RiwayatPendidikan extends Model
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
                'created' => "Menambah Riwayat Pendidikan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'updated' => "Mengubah Riwayat Pendidikan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'deleted' => "Menghapus Riwayat Pendidikan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                default   => "{$eventName} Riwayat Pendidikan untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
            });
    }
    protected $fillable = [
        'pegawai_id', 'tingkat_pendidikan', 'institusi', 'jurusan',
        'tahun_lulus', 'no_ijazah', 'tanggal_ijazah',
        'file_pdf_path', 'google_drive_link',
    ];

    protected $casts = [
        'tanggal_ijazah' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
