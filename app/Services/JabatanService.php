<?php

namespace App\Services;

use App\Models\Jabatan;
use Illuminate\Database\Eloquent\Collection;

class JabatanService
{
    public function getAllOrderedByName(): Collection
    {
        return Jabatan::orderBy('nama_jabatan')->get();
    }

    public function getActiveOrderedByName(): Collection
    {
        return Jabatan::where('is_active', true)->orderBy('nama_jabatan')->get();
    }

    public function getAllPaginated(int $perPage = 20, ?string $search = null, ?int $rumpun = null, ?string $status = null)
    {
        return Jabatan::query()
            ->when($search, fn ($q) => $q->where('nama_jabatan', 'like', "%{$search}%"))
            ->when($rumpun !== null, fn ($q) => $q->where('rumpun', $rumpun))
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('jenis_jabatan')
            ->orderBy('nama_jabatan')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $data): Jabatan
    {
        return Jabatan::create($data);
    }

    public function update(Jabatan $jabatan, array $data): Jabatan
    {
        $jabatan->update($data);
        return $jabatan;
    }

    public function toggleActive(Jabatan $jabatan): Jabatan
    {
        $jabatan->update(['is_active' => !$jabatan->is_active]);
        return $jabatan;
    }

    public function destroy(Jabatan $jabatan): void
    {
        $jabatan->delete();
    }
}
