<?php

namespace App\Http\Controllers;

use App\Services\KenaikanPangkatService;

class KenaikanPangkatController extends Controller
{
    public function __construct(private KenaikanPangkatService $service) {}

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
}
