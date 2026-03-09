<?php

namespace App\Http\Controllers;

use App\Models\GolonganPangkat;
use App\Models\TabelGaji;
use App\Services\GolonganPangkatService;
use Illuminate\Http\Request;

class GolonganController extends Controller
{
    public function __construct(
        private GolonganPangkatService $service,
    ) {}

    public function index()
    {
        $gajiStats = TabelGaji::selectRaw('golongan_id, MIN(gaji_pokok) as gaji_min, MAX(gaji_pokok) as gaji_max, COUNT(*) as jumlah_mkg')
            ->groupBy('golongan_id')
            ->get()
            ->keyBy('golongan_id');

        $golonganList = $this->service->getAll()->map(function (GolonganPangkat $gp) use ($gajiStats) {
            $stat = $gajiStats->get($gp->id);
            $gp->gaji_min = $stat?->gaji_min;
            $gp->gaji_max = $stat?->gaji_max;
            $gp->jumlah_mkg = $stat?->jumlah_mkg ?? 0;
            return $gp;
        });

        return view('admin.golongan.index', compact('golonganList'));
    }

    public function create()
    {
        return view('admin.golongan.form', [
            'golonganPangkat' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'golongan_ruang' => 'required|integer|min:1|unique:golongan_pangkats,golongan_ruang',
            'label' => 'required|string|max:10',
            'pangkat' => 'required|string|max:50',
            'golongan_group' => 'required|string|max:5',
            'min_pendidikan' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = true;

        $this->service->store($validated);

        return redirect()->route('admin.golongan.index')
            ->with('success', "Golongan {$validated['label']} — {$validated['pangkat']} berhasil ditambahkan.");
    }

    public function edit(GolonganPangkat $golonganPangkat)
    {
        return view('admin.golongan.form', [
            'golonganPangkat' => $golonganPangkat,
        ]);
    }

    public function update(Request $request, GolonganPangkat $golonganPangkat)
    {
        $validated = $request->validate([
            'pangkat' => 'required|string|max:50',
            'min_pendidikan' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $this->service->update($golonganPangkat, $validated);

        return redirect()->route('admin.golongan.index')
            ->with('success', "Golongan {$golonganPangkat->label} — {$validated['pangkat']} berhasil diperbarui.");
    }

    public function toggleActive(GolonganPangkat $golonganPangkat)
    {
        $this->service->toggleActive($golonganPangkat);
        $status = $golonganPangkat->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.golongan.index')
            ->with('success', "Golongan {$golonganPangkat->label} berhasil {$status}.");
    }

    public function destroy(GolonganPangkat $golonganPangkat)
    {
        $label = $golonganPangkat->label;
        $this->service->destroy($golonganPangkat);

        return redirect()->route('admin.golongan.index')
            ->with('success', "Golongan {$label} berhasil dihapus.");
    }
}
