<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PenilaianKinerja extends Model
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
                'created' => "Menambah Penilaian Kinerja/SKP untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'updated' => "Mengubah Penilaian Kinerja/SKP untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                'deleted' => "Menghapus Penilaian Kinerja/SKP untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
                default   => "{$eventName} Penilaian Kinerja/SKP untuk pegawai #{$this->pegawai_id} atas nama {$nama}",
            });
    }
    protected $fillable = [
        'pegawai_id', 'tahun', 'nilai_skp',
        'file_pdf_path', 'google_drive_link',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
