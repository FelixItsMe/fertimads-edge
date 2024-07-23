<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index() : View {
        $activityLog = Activity::query()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.management.activity-log.index', compact('activityLog'));
    }
}
