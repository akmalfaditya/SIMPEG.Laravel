<?php

namespace App\Services;

use App\Models\Jabatan;

class JabatanService
{
    public function getAllOrderedByName()
    {
        return Jabatan::orderBy('nama_jabatan')->get();
    }
}
