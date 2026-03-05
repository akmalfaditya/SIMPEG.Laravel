<?php

namespace App\Http\Controllers;

use App\Services\PensiunService;

class PensiunController extends Controller
{
    public function __construct(private PensiunService $service) {}

    public function index()
    {
        $alerts = $this->service->getPensiunAlerts();
        return view('pensiun.index', compact('alerts'));
    }
}
