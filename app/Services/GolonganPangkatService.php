<?php

namespace App\Services;

use App\Models\GolonganPangkat;
use Illuminate\Database\Eloquent\Collection;

class GolonganPangkatService
{
    public function getAll(): Collection
    {
        return GolonganPangkat::orderBy('golongan_ruang')->get();
    }

    public function getActive(): Collection
    {
        return GolonganPangkat::where('is_active', true)->orderBy('golongan_ruang')->get();
    }

    public function getById(int $id): GolonganPangkat
    {
        return GolonganPangkat::findOrFail($id);
    }

    public function store(array $data): GolonganPangkat
    {
        return GolonganPangkat::create($data);
    }

    public function update(GolonganPangkat $golonganPangkat, array $data): GolonganPangkat
    {
        $golonganPangkat->update($data);
        return $golonganPangkat;
    }

    public function toggleActive(GolonganPangkat $golonganPangkat): GolonganPangkat
    {
        $golonganPangkat->update(['is_active' => !$golonganPangkat->is_active]);
        return $golonganPangkat;
    }

    public function destroy(GolonganPangkat $golonganPangkat): void
    {
        $golonganPangkat->delete();
    }
}
