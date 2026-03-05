<?php

namespace App\Http\Controllers;

use App\Services\DUKService;

class DUKController extends Controller
{
    public function __construct(private DUKService $service) {}

    public function index()
    {
        $entries = $this->service->getDUK();
        return view('duk.index', compact('entries'));
    }
}
