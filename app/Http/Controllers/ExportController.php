<?php

namespace App\Http\Controllers;

use App\Exports\DUKExport;
use App\Exports\KGBExport;
use App\Exports\PensiunExport;
use App\Exports\SatyalencanaExport;
use App\Exports\KenaikanPangkatExport;
use App\Services\DUKService;
use App\Services\KGBService;
use App\Services\PensiunService;
use App\Services\SatyalencanaService;
use App\Services\KenaikanPangkatService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function __construct(
        private DUKService $dukService,
        private KGBService $kgbService,
        private PensiunService $pensiunService,
        private SatyalencanaService $satyalencanaService,
        private KenaikanPangkatService $kenaikanPangkatService,
    ) {}

    public function export(string $type, string $format)
    {
        $data = match ($type) {
            'duk' => $this->dukService->getDUK(),
            'kgb' => $this->kgbService->getAllKGBStatus(),
            'pensiun' => $this->pensiunService->getPensiunAlerts(),
            'satyalencana' => $this->satyalencanaService->getEligibleCandidates(),
            'kenaikan-pangkat' => $this->kenaikanPangkatService->getEligiblePegawai(),
            default => abort(404),
        };

        $title = match ($type) {
            'duk' => 'Daftar Urut Kepangkatan (DUK)',
            'kgb' => 'Monitoring KGB',
            'pensiun' => 'Alert Pensiun',
            'satyalencana' => 'Kandidat Satyalencana',
            'kenaikan-pangkat' => 'Kenaikan Pangkat',
        };

        $filename = str_replace(' ', '_', strtolower($title)) . '_' . date('Y-m-d');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView("exports.{$type}-pdf", compact('data', 'title'))
                ->setPaper('a4', 'landscape');
            return $pdf->download("{$filename}.pdf");
        }

        if ($format === 'excel') {
            $export = match ($type) {
                'duk' => new DUKExport($data),
                'kgb' => new KGBExport($data),
                'pensiun' => new PensiunExport($data),
                'satyalencana' => new SatyalencanaExport($data),
                'kenaikan-pangkat' => new KenaikanPangkatExport($data),
            };
            return Excel::download($export, "{$filename}.xlsx");
        }

        abort(404);
    }
}
