<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class MasterDataController extends Controller
{
    private const ENTITIES = [
        'tipe-pegawai'        => ['model' => \App\Models\TipePegawai::class,           'label' => 'Tipe Pegawai'],
        'status-kepegawaian'  => ['model' => \App\Models\StatusKepegawaian::class,      'label' => 'Status Kepegawaian'],
        'bagian'              => ['model' => \App\Models\Bagian::class,                 'label' => 'Bagian'],
        'unit-kerja'          => ['model' => \App\Models\UnitKerja::class,              'label' => 'Unit Kerja'],
        'jenis-kelamin'       => ['model' => \App\Models\JenisKelaminMaster::class,     'label' => 'Jenis Kelamin'],
        'agama'               => ['model' => \App\Models\AgamaMaster::class,            'label' => 'Agama'],
        'status-pernikahan'   => ['model' => \App\Models\StatusPernikahanMaster::class, 'label' => 'Status Pernikahan'],
        'golongan-darah'      => ['model' => \App\Models\GolonganDarahMaster::class,    'label' => 'Golongan Darah'],
    ];

    private function resolve(string $entity): array
    {
        abort_unless(isset(self::ENTITIES[$entity]), 404);
        return self::ENTITIES[$entity];
    }

    public function index(string $entity)
    {
        $cfg   = $this->resolve($entity);
        $items = $cfg['model']::orderBy('nama')->get();

        return view('admin.master-data.index', [
            'items'  => $items,
            'entity' => $entity,
            'label'  => $cfg['label'],
        ]);
    }

    public function create(string $entity)
    {
        $cfg = $this->resolve($entity);

        return view('admin.master-data.form', [
            'item'   => null,
            'entity' => $entity,
            'label'  => $cfg['label'],
        ]);
    }

    public function store(Request $request, string $entity)
    {
        $cfg       = $this->resolve($entity);
        $table     = (new $cfg['model'])->getTable();
        $validated = $request->validate([
            'nama' => "required|string|max:100|unique:{$table},nama",
        ]);

        $cfg['model']::create($validated);

        return redirect()->route('admin.master-data.index', $entity)
            ->with('success', "{$cfg['label']} \"{$validated['nama']}\" berhasil ditambahkan.");
    }

    public function edit(string $entity, int $id)
    {
        $cfg  = $this->resolve($entity);
        $item = $cfg['model']::findOrFail($id);

        return view('admin.master-data.form', [
            'item'   => $item,
            'entity' => $entity,
            'label'  => $cfg['label'],
        ]);
    }

    public function update(Request $request, string $entity, int $id)
    {
        $cfg   = $this->resolve($entity);
        $item  = $cfg['model']::findOrFail($id);
        $table = $item->getTable();

        $validated = $request->validate([
            'nama' => "required|string|max:100|unique:{$table},nama,{$id}",
        ]);

        $item->update($validated);

        return redirect()->route('admin.master-data.index', $entity)
            ->with('success', "{$cfg['label']} berhasil diperbarui.");
    }

    public function destroy(string $entity, int $id)
    {
        $cfg  = $this->resolve($entity);
        $item = $cfg['model']::findOrFail($id);
        $nama = $item->nama;
        $item->delete();

        return redirect()->route('admin.master-data.index', $entity)
            ->with('success', "{$cfg['label']} \"{$nama}\" berhasil dihapus.");
    }
}
