<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TipePegawai extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Master Tipe Pegawai #{$this->id} ({$this->nama})",
                'updated' => "Mengubah Master Tipe Pegawai #{$this->id} ({$this->nama})",
                'deleted' => "Menghapus Master Tipe Pegawai #{$this->id} ({$this->nama})",
                default   => "{$eventName} Master Tipe Pegawai #{$this->id} ({$this->nama})",
            });
    }

    protected $fillable = ['nama'];

    public $timestamps = false;

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }
}
