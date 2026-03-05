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

    public function award(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'milestone' => 'required|integer|in:10,20,30',
            'nomor_sk' => 'nullable|string|max:255',
            'tanggal_sk' => 'nullable|date',
        ]);

        $this->service->awardCandidate(
            $request->input('pegawai_id'),
            $request->input('milestone'),
            [
                'tahun' => date('Y'),
                'nomor_sk' => $request->input('nomor_sk'),
                'tanggal_sk' => $request->input('tanggal_sk'),
            ]
        );

        return redirect()->route('satyalencana.index')->with('success', 'Penghargaan berhasil dicatat.');
    }
}
