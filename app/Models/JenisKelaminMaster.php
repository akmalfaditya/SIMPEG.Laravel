<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class JenisKelaminMaster extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Menambah Master Jenis Kelamin #{$this->id} ({$this->nama})",
                'updated' => "Mengubah Master Jenis Kelamin #{$this->id} ({$this->nama})",
                'deleted' => "Menghapus Master Jenis Kelamin #{$this->id} ({$this->nama})",
                default   => "{$eventName} Master Jenis Kelamin #{$this->id} ({$this->nama})",
            });
    }

    protected $table = 'jenis_kelamins';

    protected $fillable = ['nama'];

    public $timestamps = false;

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'jenis_kelamin_id');
    }
}
