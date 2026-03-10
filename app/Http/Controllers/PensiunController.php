<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPensiunRequest;
use App\Services\PensiunService;
use Illuminate\Http\Request;

class PensiunController extends Controller
{
    public function __construct(private PensiunService $service) {}

    public function index(Request $request)
    {
        $filterLevel = $request->input('level');
        $alerts = $this->service->getPensiunAlerts();

        if ($filterLevel && in_array($filterLevel, ['Hitam', 'Merah', 'Kuning', 'Hijau'])) {
            $alerts = array_values(array_filter($alerts, fn($a) => $a['alert_level'] === $filterLevel));
        }

        return view('pensiun.index', compact('alerts', 'filterLevel'));
    }

    public function showProcessForm(int $pegawaiId)
    {
        $data = $this->service->getProcessData($pegawaiId);

        if (!$data) {
            return redirect()->route('pensiun.index')
                ->with('error', 'Data pegawai tidak ditemukan atau sudah tidak aktif.');
        }

        if ($data['blocked']) {
            return redirect()->route('pensiun.index')
                ->with('error', $data['blocked_reason']);
        }

        return view('pensiun.process', ['data' => $data]);
    }

    public function process(ProcessPensiunRequest $request)
    {
        $this->service->processPensiun($request->validated());

        return redirect()->route('pensiun.index')
            ->with('success', 'Pensiun berhasil diproses. Status pegawai telah diubah menjadi Pensiun dan dinonaktifkan.');
    }
}
