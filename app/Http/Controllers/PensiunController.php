<?php

namespace App\Http\Controllers;

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
}
