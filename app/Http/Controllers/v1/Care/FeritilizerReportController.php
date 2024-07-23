<?php

namespace App\Http\Controllers\v1\Care;

use App\Exports\FertilizationReportExport;
use App\Exports\FertilizationReportPDFExport;
use App\Http\Controllers\Controller;
use App\Models\DeviceReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FeritilizerReportController extends Controller
{
    public function index()
    {
        $reports = DeviceReport::query()
            ->where('type', 'like', '%pemupukan%')
            ->with('deviceSelenoid.garden.land')
            ->latest()
            ->paginate(10);

        $reportsCount = DeviceReport::count();

        return view('pages.care.fertilizer-report.index', compact('reports', 'reportsCount'));
    }

    public function export()
    {
        return Excel::download(new FertilizationReportExport, 'laporan_pemupukan.xlsx');
    }

    public function pdf()
    {
        $reports = DeviceReport::query()
            ->where('type', 'like', '%pemupukan%')
            ->with('deviceSelenoid.garden.land')
            ->latest()
            ->get();

        $pdf = Pdf::loadView('exports.fertilization-report', ['reports' => $reports])->setPaper('A4', 'landscape');

        return $pdf->stream();
    }
}
