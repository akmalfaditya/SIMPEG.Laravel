<?php

namespace App\Http\Controllers;

use App\Enums\GolonganRuang;
use App\Models\TabelGaji;
use App\Services\TabelGajiService;
use Illuminate\Http\Request;

class TabelGajiController extends Controller
{
    public function __construct(
        private TabelGajiService $service,
    ) {}

    public function index()
    {
        return view('admin.tabel-gaji.index', [
            'summary' => $this->service->getSummaryPerGolongan(),
        ]);
    }

    public function show(int $golongan)
    {
        $golonganEnum = GolonganRuang::tryFrom($golongan);
        if (!$golonganEnum) {
            abort(404, 'Golongan tidak valid.');
        }

        return view('admin.tabel-gaji.show', [
            'golongan' => $golonganEnum,
            'entries' => $this->service->getByGolongan($golongan),
            'allGolongan' => GolonganRuang::cases(),
        ]);
    }

    public function update(Request $request, TabelGaji $tabelGaji)
    {
        $validated = $request->validate([
            'gaji_pokok' => 'required|integer|min:0',
        ]);

        $this->service->update($tabelGaji, $validated['gaji_pokok']);

        return redirect()->route('admin.tabel-gaji.show', $tabelGaji->golongan_ruang->value)
            ->with('success', "Gaji pokok MKG {$tabelGaji->masa_kerja_tahun} tahun berhasil diperbarui.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'golongan_ruang' => 'required|integer|min:1|max:17',
            'masa_kerja_tahun' => 'required|integer|min:0|max:40',
            'gaji_pokok' => 'required|integer|min:0',
        ]);

        $exists = TabelGaji::where('golongan_ruang', $validated['golongan_ruang'])
            ->where('masa_kerja_tahun', $validated['masa_kerja_tahun'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['masa_kerja_tahun' => 'MKG tersebut sudah ada untuk golongan ini.'])->withInput();
        }

        $this->service->store($validated['golongan_ruang'], $validated['masa_kerja_tahun'], $validated['gaji_pokok']);

        return redirect()->route('admin.tabel-gaji.show', $validated['golongan_ruang'])
            ->with('success', 'Entri gaji pokok baru berhasil ditambahkan.');
    }

    public function destroy(TabelGaji $tabelGaji)
    {
        $golongan = $tabelGaji->golongan_ruang->value;
        $this->service->delete($tabelGaji);

        return redirect()->route('admin.tabel-gaji.show', $golongan)
            ->with('success', 'Entri gaji pokok berhasil dihapus.');
    }
}
