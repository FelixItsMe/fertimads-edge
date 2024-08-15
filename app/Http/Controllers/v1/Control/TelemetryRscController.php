<?php

namespace App\Http\Controllers\v1\Control;

use App\Exports\DeviceTelemetryExport;
use App\Http\Controllers\Controller;
use App\Models\DeviceTelemetry;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $deviceTelemetries = DeviceTelemetry::query()
            ->when(($queryFrom == $queryTo), function(Builder $query, $a)use($queryFrom){
                $query->whereDate('created_at', $queryFrom);
            })
            ->when(($queryFrom != $queryTo), function(Builder $query, $a)use($queryFrom, $queryTo){
                $query->where('created_at', '>=', $queryFrom)
                ->where('created_at', '<=', $queryTo);
            })
            ->get();

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
