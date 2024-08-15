<?php

namespace App\Exports;

use App\Models\DeviceTelemetry;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DeviceTelemetryExport implements FromView, ShouldAutoSize
{
    public function __construct(protected Collection $deviceTelemetries)
    {
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.excel.device-telemetry', [
            'deviceTelemetries' => $this->deviceTelemetries
        ]);
    }
}
