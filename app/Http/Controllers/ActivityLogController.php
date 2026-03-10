<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer', 'subject')->latest();

        if ($search = $request->input('search')) {
            $query->where('description', 'like', '%' . $search . '%');
        }

        $activities = $query->paginate(20)->withQueryString();

        return view('activity-log.index', compact('activities'));
    }
}
