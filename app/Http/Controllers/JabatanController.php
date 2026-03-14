<?php

namespace App\Http\Controllers;

use App\Enums\JenisJabatan;
use App\Models\Jabatan;
use App\Models\RumpunJabatan;
use App\Services\JabatanService;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function __construct(
        private JabatanService $service,
    ) {}

    public function index(Request $request)
    {
        return view('admin.jabatan.index', [
            'jabatans' => $this->service->getAllPaginated(
                perPage: 20,
                search: $request->query('search'),
                rumpun: $request->query('rumpun') !== null ? (int) $request->query('rumpun') : null,
                status: $request->query('status'),
            ),
            'jenisJabatanList' => JenisJabatan::cases(),
            'rumpunList' => RumpunJabatan::orderBy('nama')->get(),
            'filterRumpun' => $request->query('rumpun'),
            'filterSearch' => $request->query('search'),
            'filterStatus' => $request->query('status'),
        ]);
    }

    public function create()
    {
        return view('admin.jabatan.form', [
            'jabatan' => null,
            'jenisJabatanList' => JenisJabatan::cases(),
            'rumpunList' => RumpunJabatan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'jenis_jabatan' => 'required|integer|min:1|max:6',
            'rumpun_jabatan_id' => 'required|exists:rumpun_jabatans,id',
            'bup' => 'required|integer|min:50|max:70',
            'eselon_level' => 'nullable|integer|min:0|max:5',
            'kelas_jabatan' => 'nullable|integer|min:1|max:17',
        ]);

        $validated['eselon_level'] = $validated['eselon_level'] ?? 0;
        $validated['kelas_jabatan'] = $validated['kelas_jabatan'] ?? 1;
        $validated['is_active'] = true;

        $this->service->store($validated);

        return redirect()->route('admin.jabatan.index')
            ->with('success', "Jabatan \"{$validated['nama_jabatan']}\" berhasil ditambahkan.");
    }

    public function edit(Jabatan $jabatan)
    {
        return view('admin.jabatan.form', [
            'jabatan' => $jabatan,
            'jenisJabatanList' => JenisJabatan::cases(),
            'rumpunList' => RumpunJabatan::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'jenis_jabatan' => 'required|integer|min:1|max:6',
            'rumpun_jabatan_id' => 'required|exists:rumpun_jabatans,id',
            'bup' => 'required|integer|min:50|max:70',
            'eselon_level' => 'nullable|integer|min:0|max:5',
            'kelas_jabatan' => 'nullable|integer|min:1|max:17',
        ]);

        $validated['eselon_level'] = $validated['eselon_level'] ?? 0;
        $validated['kelas_jabatan'] = $validated['kelas_jabatan'] ?? 1;

        $this->service->update($jabatan, $validated);

        return redirect()->route('admin.jabatan.index')
            ->with('success', "Jabatan \"{$validated['nama_jabatan']}\" berhasil diperbarui.");
    }

    public function toggleActive(Jabatan $jabatan)
    {
        $this->service->toggleActive($jabatan);
        $status = $jabatan->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.jabatan.index')
            ->with('success', "Jabatan \"{$jabatan->nama_jabatan}\" berhasil {$status}.");
    }

    public function destroy(Jabatan $jabatan)
    {
        $usageCount = $jabatan->riwayatJabatan()->count();
        if ($usageCount > 0) {
            return back()->with('error', "Jabatan \"{$jabatan->nama_jabatan}\" tidak dapat dihapus karena masih digunakan oleh {$usageCount} riwayat jabatan.");
        }

        $nama = $jabatan->nama_jabatan;
        $this->service->destroy($jabatan);

        return redirect()->route('admin.jabatan.index')
            ->with('success', "Jabatan \"{$nama}\" berhasil dihapus.");
    }
}
