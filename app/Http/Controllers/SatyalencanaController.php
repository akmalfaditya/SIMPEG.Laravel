<?php

namespace App\Http\Controllers;

use App\Services\SatyalencanaService;
use Illuminate\Http\Request;

class SatyalencanaController extends Controller
{
    public function __construct(private SatyalencanaService $service) {}

    public function index(Request $request)
    {
        $milestone = $request->input('milestone');
        $candidates = $milestone
            ? $this->service->getCandidatesByMilestone((int)$milestone)
            : $this->service->getEligibleCandidates();

        return view('satyalencana.index', [
            'candidates' => $candidates,
            'selectedMilestone' => $milestone,
        ]);
    }
}
