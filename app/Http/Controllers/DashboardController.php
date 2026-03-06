<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['unit_kerja', 'golongan', 'tmt_cpns_from', 'tmt_cpns_to']);
        $filterOptions = $this->service->getFilterOptions();
        $data = $this->service->getDashboardData($filters);
        $hasFilters = !empty(array_filter($filters));

        return view('dashboard.index', compact('data', 'filterOptions', 'hasFilters'));
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only(['unit_kerja', 'golongan', 'tmt_cpns_from', 'tmt_cpns_to']);
        $data = $this->service->getDashboardData($filters);

        $pdf = Pdf::loadView('exports.dashboard-pdf', compact('data'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('dashboard_summary_' . date('Y-m-d') . '.pdf');
    }
}
