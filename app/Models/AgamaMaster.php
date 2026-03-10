<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgamaMaster extends Model
{
    protected $table = 'agamas';

    protected $fillable = ['nama'];

    public $timestamps = false;

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'agama_id');
    }
}
