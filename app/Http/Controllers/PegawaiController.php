<?php

namespace App\Http\Controllers;

use App\Enums\Agama;
use App\Enums\GolonganDarah;
use App\Enums\JenisKelamin;
use App\Enums\StatusPernikahan;
use App\Models\Pegawai;
use App\Services\PegawaiService;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function __construct(private PegawaiService $service) {}

    public function index()
    {
        return view('pegawai.index');
    }

    public function getPaginated(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        $pegawaiList = $search
            ? $this->service->search($search)
            : $this->service->getAll();

        $total = $pegawaiList->count();
        $paged = $pegawaiList->slice(($page - 1) * $limit, $limit)->map(fn($p) => [
            $p->nip,
            $p->nama_lengkap,
            $p->pangkat_terakhir ?? '-',
            $p->jabatan_terakhir ?? '-',
            $p->masa_kerja ?? '-',
            $p->id,
        ])->values();

        return response()->json(['data' => $paged, 'total' => $total]);
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load([
            'riwayatPangkat', 'riwayatJabatan.jabatan',
            'riwayatKgb', 'riwayatHukumanDisiplin',
            'riwayatPendidikan', 'riwayatLatihanJabatan',
            'penilaianKinerja',
        ]);
        return view('pegawai.show', compact('pegawai'));
    }

    public function create()
    {
        return view('pegawai.create', [
            'jenisKelaminOptions' => JenisKelamin::cases(),
            'agamaOptions' => Agama::cases(),
            'statusPernikahanOptions' => StatusPernikahan::cases(),
            'golonganDarahOptions' => GolonganDarah::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:pegawais,nip',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|integer',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'nullable|date',
            'gaji_pokok' => 'required|numeric|min:0',
            'agama' => 'required|integer',
            'status_pernikahan' => 'required|integer',
            'golongan_darah' => 'required|integer',
            'npwp' => 'nullable|string',
            'no_karpeg' => 'nullable|string',
            'no_taspen' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
        ]);

        $pegawai = $this->service->create($validated);
        return redirect()->route('pegawai.show', $pegawai)->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', [
            'pegawai' => $pegawai,
            'jenisKelaminOptions' => JenisKelamin::cases(),
            'agamaOptions' => Agama::cases(),
            'statusPernikahanOptions' => StatusPernikahan::cases(),
            'golonganDarahOptions' => GolonganDarah::cases(),
        ]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:pegawais,nip,' . $pegawai->id,
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|integer',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'nullable|date',
            'gaji_pokok' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'agama' => 'required|integer',
            'status_pernikahan' => 'required|integer',
            'golongan_darah' => 'required|integer',
            'npwp' => 'nullable|string',
            'no_karpeg' => 'nullable|string',
            'no_taspen' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $this->service->update($pegawai, $validated);
        return redirect()->route('pegawai.show', $pegawai)->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $this->service->delete($pegawai);
        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }
}
