<?php

namespace App\Http\Controllers;

use App\Http\Requests\Riwayat\StorePangkatRequest;
use App\Services\KenaikanPangkatService;
use App\Services\RiwayatService;

class KenaikanPangkatController extends Controller
{
    public function __construct(
        private KenaikanPangkatService $service,
        private RiwayatService $riwayatService,
    ) {}

    public function index()
    {
        $candidates = $this->service->getEligiblePegawai();
        return view('kenaikan-pangkat.index', [
            'candidates' => $candidates,
            'filterTitle' => null,
            'activeFilter' => 'semua',
        ]);
    }

    public function eligible()
    {
        $all = $this->service->getEligiblePegawai();
        $eligible = array_values(array_filter($all, fn($c) => $c['is_eligible']));
        return view('kenaikan-pangkat.index', [
            'candidates' => $eligible,
            'filterTitle' => 'Pegawai Eligible Kenaikan Pangkat',
            'activeFilter' => 'eligible',
        ]);
    }

    public function ditunda()
    {
        $candidates = $this->service->getDitundaPegawai();
        return view('kenaikan-pangkat.index', [
            'candidates' => $candidates,
            'filterTitle' => 'Pegawai Ditunda Kenaikan Pangkat (Hukdis)',
            'activeFilter' => 'ditunda',
        ]);
    }

    public function showProcessForm(int $pegawaiId)
    {
        $data = $this->service->getProcessData($pegawaiId);

        if (!$data) {
            return redirect()->route('kenaikan-pangkat.eligible')
                ->with('error', 'Data pegawai tidak ditemukan atau belum memiliki riwayat pangkat.');
        }

        if ($data['blocked']) {
            return redirect()->route('kenaikan-pangkat.eligible')
                ->with('error', $data['blocked_reason']);
        }

        return view('kenaikan-pangkat.process', ['data' => $data]);
    }

    public function process(StorePangkatRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->riwayatService->uploadSk(
                $request->file('file_sk'), 'sk_pangkat', null, (int) $validated['pegawai_id']
            );
        }

        // Pass gaji_baru through for gaji_pokok update
        $validated['gaji_baru'] = $request->input('gaji_baru');

        $this->service->processKenaikanPangkat($validated);

        return redirect()->route('kenaikan-pangkat.eligible')
            ->with('success', 'Kenaikan pangkat berhasil diproses. Riwayat Pangkat & gaji pokok telah diperbarui.');
    }
}
