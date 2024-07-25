<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Imports\DailyIrrigationImport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;

class ActivityLogController extends Controller
{
    public function index() : View {
        $activityLog = Activity::query()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.management.activity-log.index', compact('activityLog'));
    }

    public function indexImport() : View {
        return view('pages.test.inport-excel');
    }

    public function storeImport(Request $request) : RedirectResponse {

        Excel::import(new DailyIrrigationImport, $request->file('test_irigasi'));
        return back();
    }
}
