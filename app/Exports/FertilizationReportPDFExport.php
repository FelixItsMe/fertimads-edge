<?php

namespace App\Exports;

use App\Models\DeviceReport;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

class FertilizationReportPDFExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $reports = DeviceReport::query()
            ->where('type', 'like', '%pemupukan%')
            ->with('deviceSelenoid.garden.land')
            ->latest()
            ->get();

        return view('exports.fertilization-report', [
            'reports' => $reports
        ]);
    }
}
