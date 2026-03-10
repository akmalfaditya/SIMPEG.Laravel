<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipePegawai extends Model
{
    protected $fillable = ['nama'];

    public $timestamps = false;

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }
}
