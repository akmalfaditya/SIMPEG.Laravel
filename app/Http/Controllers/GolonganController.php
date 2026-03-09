<?php

namespace App\Http\Controllers;

use App\Enums\GolonganRuang;
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
        $gajiStats = TabelGaji::selectRaw('golongan_ruang, MIN(gaji_pokok) as gaji_min, MAX(gaji_pokok) as gaji_max, COUNT(*) as jumlah_mkg')
            ->groupBy('golongan_ruang')
            ->get()
            ->keyBy(fn ($row) => $row->golongan_ruang->value);

        $golonganList = $this->service->getAll()->map(function (GolonganPangkat $gp) use ($gajiStats) {
            $stat = $gajiStats->get($gp->golongan_ruang->value);
            $gp->gaji_min = $stat?->gaji_min;
            $gp->gaji_max = $stat?->gaji_max;
            $gp->jumlah_mkg = $stat?->jumlah_mkg ?? 0;
            return $gp;
        });

        return view('admin.golongan.index', compact('golonganList'));
    }

    public function create()
    {
        $usedValues = GolonganPangkat::pluck('golongan_ruang')->map(fn ($g) => $g->value)->toArray();
        $availableGolongan = collect(GolonganRuang::cases())->filter(fn ($g) => !in_array($g->value, $usedValues));

        return view('admin.golongan.form', [
            'golonganPangkat' => null,
            'availableGolongan' => $availableGolongan,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'golongan_ruang' => 'required|integer|min:1|max:17|unique:golongan_pangkats,golongan_ruang',
            'pangkat' => 'required|string|max:50',
            'min_pendidikan' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $golEnum = GolonganRuang::from($validated['golongan_ruang']);
        $validated['label'] = $golEnum->label();
        $validated['golongan_group'] = substr($golEnum->label(), 0, strpos($golEnum->label(), '/'));
        $validated['is_active'] = true;

        $this->service->store($validated);

        return redirect()->route('admin.golongan.index')
            ->with('success', "Golongan {$validated['label']} — {$validated['pangkat']} berhasil ditambahkan.");
    }

    public function edit(GolonganPangkat $golonganPangkat)
    {
        return view('admin.golongan.form', [
            'golonganPangkat' => $golonganPangkat,
            'availableGolongan' => collect(),
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
