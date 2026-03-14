<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        'rumpun-jabatan'      => ['model' => \App\Models\RumpunJabatan::class,          'label' => 'Rumpun Jabatan'],
        'tingkat-pendidikan'  => ['model' => \App\Models\MasterPendidikan::class,      'label' => 'Tingkat Pendidikan'],
    ];

    private function resolve(string $entity): array
    {
        abort_unless(isset(self::ENTITIES[$entity]), 404);
        return self::ENTITIES[$entity];
    }

    public function index(Request $request, string $entity)
    {
        $cfg   = $this->resolve($entity);
        $query = $cfg['model']::orderBy('nama');

        if ($search = $request->input('search')) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        $items = $query->paginate(15)->withQueryString();

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
        $rules = [
            'nama' => "required|string|max:100|unique:{$table},nama",
        ];
        
        if ($entity === 'tingkat-pendidikan') {
            $rules['bobot'] = 'required|integer|min:1|max:99';
        }

        $validated = $request->validate($rules);

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

        $rules = [
            'nama' => "required|string|max:100|unique:{$table},nama,{$id}",
        ];

        if ($entity === 'tingkat-pendidikan') {
            $rules['bobot'] = 'required|integer|min:1|max:99';
        }

        $validated = $request->validate($rules);

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
