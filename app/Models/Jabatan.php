<?php

namespace App\Models;

use App\Enums\JenisJabatan;
use App\Enums\RumpunJabatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Jabatan extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Master Jabatan #{$this->id} ({$this->nama_jabatan})",
                'updated' => "Mengubah Master Jabatan #{$this->id} ({$this->nama_jabatan})",
                'deleted' => "Menghapus Master Jabatan #{$this->id} ({$this->nama_jabatan})",
                default   => "{$eventName} Master Jabatan #{$this->id} ({$this->nama_jabatan})",
            });
    }

    protected $fillable = [
        'nama_jabatan', 'jenis_jabatan', 'bup', 'eselon_level', 'kelas_jabatan', 'rumpun', 'is_active',
    ];

    protected $casts = [
        'jenis_jabatan' => JenisJabatan::class,
        'rumpun' => RumpunJabatan::class,
        'is_active' => 'boolean',
    ];

    public function riwayatJabatan(): HasMany
    {
        return $this->hasMany(RiwayatJabatan::class);
    }
}
