<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\PaginatesArray;
use App\Http\Requests\Riwayat\StoreKGBRequest;
use App\Services\KGBService;
use App\Services\RiwayatService;
use Illuminate\Http\Request;

class KGBController extends Controller
{
    use PaginatesArray;

    public function __construct(
        private KGBService $service,
        private RiwayatService $riwayatService,
    ) {}

    public function index(Request $request)
    {
        $alerts = $this->paginateArray($this->service->getAllKGBStatus(), $request);
        return view('kgb.index', ['alerts' => $alerts, 'filterTitle' => null]);
    }

    public function upcoming(Request $request)
    {
        $alerts = $this->paginateArray($this->service->getUpcomingKGB(60), $request);
        return view('kgb.index', ['alerts' => $alerts, 'filterTitle' => 'Pegawai H-60 Hari Menuju KGB']);
    }

    public function eligible(Request $request)
    {
        $alerts = $this->paginateArray($this->service->getEligiblePegawai(), $request);
        return view('kgb.index', ['alerts' => $alerts, 'filterTitle' => 'Pegawai Eligible KGB']);
    }

    public function ditunda(Request $request)
    {
        $alerts = $this->paginateArray($this->service->getDitundaPegawai(), $request);
        return view('kgb.index', ['alerts' => $alerts, 'filterTitle' => 'Pegawai Ditunda KGB (Hukdis)']);
    }

    public function showProcessForm(int $pegawaiId)
    {
        $data = $this->service->getProcessData($pegawaiId);

        if (!$data) {
            return redirect()->route('kgb.eligible')
                ->with('error', 'Data pegawai tidak ditemukan atau belum memiliki riwayat KGB.');
        }

        if ($data['blocked']) {
            return redirect()->route('kgb.eligible')
                ->with('error', $data['blocked_reason']);
        }

        return view('kgb.process', ['data' => $data]);
    }

    public function process(StoreKGBRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_sk')) {
            $validated['file_pdf_path'] = $this->riwayatService->uploadSk(
                $request->file('file_sk'),
                'sk_kgb',
                null,
                (int) $validated['pegawai_id']
            );
        }

        $this->service->processKGB($validated);

        return redirect()->route('kgb.eligible')
            ->with('success', 'KGB berhasil diproses untuk pegawai. Riwayat KGB & gaji pokok telah diperbarui.');
    }
}
