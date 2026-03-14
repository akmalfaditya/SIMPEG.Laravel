<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MasterPendidikan extends Model
{
    use LogsActivity;

    protected $fillable = ['nama', 'bobot'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Master Pendidikan #{$this->id} ({$this->nama})",
                'updated' => "Mengubah Master Pendidikan #{$this->id} ({$this->nama})",
                'deleted' => "Menghapus Master Pendidikan #{$this->id} ({$this->nama})",
                default   => "{$eventName} Master Pendidikan #{$this->id} ({$this->nama})",
            });
    }

    public function riwayatPendidikan(): HasMany
    {
        return $this->hasMany(RiwayatPendidikan::class, 'pendidikan_id');
    }
}
