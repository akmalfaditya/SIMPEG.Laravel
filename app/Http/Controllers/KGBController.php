<?php

namespace App\Http\Controllers;

use App\Services\KGBService;

class KGBController extends Controller
{
    public function __construct(private KGBService $service) {}

    public function index()
    {
        $alerts = $this->service->getAllKGBStatus();
        return view('kgb.index', ['alerts' => $alerts, 'filterTitle' => null]);
    }

    public function upcoming()
    {
        $alerts = $this->service->getUpcomingKGB(60);
        return view('kgb.index', ['alerts' => $alerts, 'filterTitle' => 'Pegawai H-60 Hari Menuju KGB']);
    }

    public function eligible()
    {
        $alerts = $this->service->getEligiblePegawai();
        return view('kgb.index', ['alerts' => $alerts, 'filterTitle' => 'Pegawai Eligible KGB']);
    }
}
