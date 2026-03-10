<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\PaginatesArray;
use App\Services\DUKService;
use Illuminate\Http\Request;

class DUKController extends Controller
{
    use PaginatesArray;

    public function __construct(private DUKService $service) {}

    public function index(Request $request)
    {
        $entries = $this->paginateArray($this->service->getDUK(), $request);
        return view('duk.index', compact('entries'));
    }
}
