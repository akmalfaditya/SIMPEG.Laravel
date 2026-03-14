<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RumpunJabatan extends Model
{
    use LogsActivity;

    protected $fillable = ['nama'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Rumpun Jabatan #{$this->id} ({$this->nama})",
                'updated' => "Mengubah Rumpun Jabatan #{$this->id} ({$this->nama})",
                'deleted' => "Menghapus Rumpun Jabatan #{$this->id} ({$this->nama})",
                default   => "{$eventName} Rumpun Jabatan #{$this->id} ({$this->nama})",
            });
    }

    public function jabatans(): HasMany
    {
        return $this->hasMany(Jabatan::class);
    }
}
