<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\DailyIrrigation\StoreDailyIrrigationRequest;
use App\Imports\DailyIrrigationImport;
use App\Models\DailyIrrigation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DailyIrrigationController extends Controller
{
    public function index() : View {
        $dailyIrrigations = DailyIrrigation::query()
            ->orderBy('date')
            ->paginate(10);

        return view('pages.management.daily-irrigation.index', compact('dailyIrrigations'));
    }
    public function create() : View {
        return view('pages.management.daily-irrigation.create');
    }

    public function store(StoreDailyIrrigationRequest $request) {
        Excel::import(new DailyIrrigationImport, $request->file('import_excel'));

        return redirect(route('daily-irrigation.index'))->with('daily-irrigation-success', 'Berhasil diimport');
    }
}
