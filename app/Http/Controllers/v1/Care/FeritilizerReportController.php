<?php

namespace App\Http\Controllers\v1\Care;

use App\Exports\FertilizationReportExport;
use App\Http\Controllers\Controller;
use App\Models\DeviceReport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FeritilizerReportController extends Controller
{
    public function index()
    {
        $reports = DeviceReport::query()
            ->where('type', 'like', '%pemupukan%')
            ->with('deviceSelenoid.garden')
            ->paginate(10);

        return view('pages.care.fertilizer-report.index', compact('reports'));
    }

    public function export()
    {
        return Excel::download(new FertilizationReportExport, 'laporan_pemupukan.xlsx');
    }
}
