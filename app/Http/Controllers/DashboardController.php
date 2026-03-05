<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index()
    {
        $data = $this->service->getDashboardData();
        return view('dashboard.index', compact('data'));
    }
}
