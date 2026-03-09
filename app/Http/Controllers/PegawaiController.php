<?php

namespace App\Http\Controllers;

use App\Enums\Agama;
use App\Enums\GolonganDarah;
use App\Enums\GolonganRuang;
use App\Enums\JenisKelamin;
use App\Enums\StatusPernikahan;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Services\PegawaiService;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\DTOs\PegawaiDTO;
use App\Http\Resources\PegawaiResource;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_LIMIT = 10;

    public function __construct(private PegawaiService $service) {}

    public function index()
    {
        return view('pegawai.index');
    }

    public function getPaginated(Request $request)
    {
        $page = $request->input('page', self::DEFAULT_PAGE);
        $limit = $request->input('limit', self::DEFAULT_LIMIT);
        $search = $request->input('search');

        $pegawaiList = $search
            ? $this->service->search($search)
            : $this->service->getAll();

        $total = $pegawaiList->count();
        $pagedModels = $pegawaiList->slice(($page - 1) * $limit, $limit)->values();

        // Convert to explicitly structured Resource
        $paged = PegawaiResource::collection($pagedModels);

        return response()->json(['data' => $paged, 'total' => $total]);
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load([
            'riwayatPangkat', 'riwayatJabatan.jabatan',
            'riwayatKgb', 'riwayatHukumanDisiplin',
            'riwayatPendidikan', 'riwayatLatihanJabatan',
            'penilaianKinerja', 'riwayatPenghargaan',
        ]);
        return view('pegawai.show', [
            'pegawai' => $pegawai,
            'golonganOptions' => GolonganRuang::cases(),
            'jabatanOptions' => Jabatan::orderBy('nama_jabatan')->get(),
        ]);
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

    public function store(StorePegawaiRequest $request)
    {
        $dto = PegawaiDTO::fromRequest($request->validated());
        $pegawai = $this->service->create($dto);

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

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        $dto = PegawaiDTO::fromRequest($request->validated());
        $this->service->update($pegawai, $dto);

        return redirect()->route('pegawai.show', $pegawai)->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $this->service->delete($pegawai);
        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }
}
