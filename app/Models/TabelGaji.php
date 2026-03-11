<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TabelGaji extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $golLabel = $this->golongan->label ?? 'Unknown';
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Master Tabel Gaji #{$this->id} (Gol: {$golLabel}, MKG: {$this->masa_kerja_tahun})",
                'updated' => "Mengubah Master Tabel Gaji #{$this->id} (Gol: {$golLabel}, MKG: {$this->masa_kerja_tahun})",
                'deleted' => "Menghapus Master Tabel Gaji #{$this->id} (Gol: {$golLabel}, MKG: {$this->masa_kerja_tahun})",
                default   => "{$eventName} Master Tabel Gaji #{$this->id} (Gol: {$golLabel}, MKG: {$this->masa_kerja_tahun})",
            });
    }

    protected $fillable = [
        'golongan_id',
        'masa_kerja_tahun',
        'gaji_pokok',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
    ];

    public function golongan(): BelongsTo
    {
        return $this->belongsTo(GolonganPangkat::class, 'golongan_id');
    }
}
