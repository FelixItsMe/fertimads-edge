<?php

namespace App\Http\Controllers\v1\Control;

use App\Exports\DeviceTelemetryExport;
use App\Http\Controllers\Controller;
use App\Models\DeviceTelemetry;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TelemetryRscController extends Controller
{
    public function index() : View {
        $deviceTelemetries = DeviceTelemetry::query()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.control.telemetry-rsc.index', compact('deviceTelemetries'));
    }

    public function excelExport() : BinaryFileResponse|RedirectResponse {
        $queryFrom = request()->query('from');
        $queryTo = request()->query('to');

        Validator::make(
                [
                    'from' => $queryFrom,
                    'to' => $queryTo
                ],
                [
                    'from' => 'required|date|date_format:Y-m-d',
                    'to' => 'required|date|after_or_equal:from|date_format:Y-m-d',
                ],
                [],
                [
                    'from' => 'Awal',
                    'to' => 'Akhir',
                ]
            )
            ->validate();

        $deviceTelemetries = [];

        DeviceTelemetry::query()
            ->when(($queryFrom == $queryTo), function(Builder $query, $a)use($queryFrom){
                $query->whereDate('created_at', $queryFrom);
            })
            ->when(($queryFrom != $queryTo), function(Builder $query, $a)use($queryFrom, $queryTo){
                $query->where('created_at', '>=', $queryFrom)
                ->where('created_at', '<=', $queryTo);
            })
            ->chunk(200, function(Collection $data)use(&$deviceTelemetries){
                $formatData = [];
                foreach ($data as $val) {
                    $telemetry = (array) $val->telemetry;
                    for ($i=1; $i <= 4; $i++) {
                        $formatData[] = (object) [
                            'created_at' => $val->created_at,
                            'selenoid' => $i,
                            'N' => number_format($telemetry['SS' . $i]->N, 2) . ' mg/kg',
                            'P' => number_format($telemetry['SS' . $i]->P, 2) . ' mg/kg',
                            'K' => number_format($telemetry['SS' . $i]->K, 2) . ' mg/kg',
                            'EC' => number_format($telemetry['SS' . $i]->EC, 2) . ' uS/cm',
                            'pH' => number_format($telemetry['SS' . $i]->pH, 2),
                            'T' => number_format($telemetry['SS' . $i]->T, 2) . "°C",
                            'H' => number_format($telemetry['SS' . $i]->H, 2) . "%",
                            'dhtT' => number_format($telemetry['DHT1']->T, 2) . "°C",
                            'dhtH' => number_format($telemetry['DHT1']->H, 2) . "%",
                        ];
                    }
                }
                $deviceTelemetries = [
                    ...$deviceTelemetries,
                    ...$formatData
                ];
            });

        $deviceTelemetries = collect($deviceTelemetries);

        if ($deviceTelemetries->count() == 0) {
            return back()->withErrors([
                'deviceTelemetries' => [
                    'Data tidak ada untuk range tanggal yang dipilih'
                ]
            ]);
        }

        return Excel::download(new DeviceTelemetryExport($deviceTelemetries), 'rsc-telemetri.xlsx');
    }
}
