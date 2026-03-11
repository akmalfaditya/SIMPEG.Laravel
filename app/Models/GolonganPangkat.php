<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class GolonganPangkat extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Master Golongan #{$this->id} ({$this->label})",
                'updated' => "Mengubah Master Golongan #{$this->id} ({$this->label})",
                'deleted' => "Menghapus Master Golongan #{$this->id} ({$this->label})",
                default   => "{$eventName} Master Golongan #{$this->id} ({$this->label})",
            });
    }

    protected $fillable = [
        'golongan_ruang',
        'label',
        'pangkat',
        'golongan_group',
        'min_pendidikan',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function riwayatPangkat(): HasMany
    {
        return $this->hasMany(RiwayatPangkat::class, 'golongan_id');
    }

    public function tabelGaji(): HasMany
    {
        return $this->hasMany(TabelGaji::class, 'golongan_id');
    }
}
