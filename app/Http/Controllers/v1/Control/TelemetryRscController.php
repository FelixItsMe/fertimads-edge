<?php

namespace App\Http\Controllers\v1\Control;

use App\Http\Controllers\Controller;
use App\Jobs\ExportRscTelemetryJob;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Models\DeviceTelemetry;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TelemetryRscController extends Controller
{
    public function index() : View {
        $deviceTelemetries = DeviceTelemetry::query()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.control.telemetry-rsc.index', compact('deviceTelemetries'));
    }

    public function excelExport() : JsonResponse {
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

        $deviceTelemetriesCount = DeviceTelemetry::query()
            ->when(($queryFrom == $queryTo), function(Builder $query, $a)use($queryFrom){
                $query->whereDate('created_at', $queryFrom);
            })
            ->when(($queryFrom != $queryTo), function(Builder $query, $a)use($queryFrom, $queryTo){
                $query->where('created_at', '>=', $queryFrom)
                ->where('created_at', '<=', $queryTo);
            })
            ->count();

        if ($deviceTelemetriesCount == 0) {
            return response()->json([
                'message' => 'Data tidak ada untuk range tanggal yang dipilih',
                'errors' => [
                    'deviceTelemetries' => [
                        'Data tidak ada untuk range tanggal yang dipilih'
                    ]
                ]
            ], 400);
        }

        ExportRscTelemetryJob::dispatch($queryFrom, $queryTo, request()->user())
            ->onQueue('export-telemetry-rsc');
            // ->chain([
            //     new NotifyUserOfCompletedExport(request()->user()),
            // ]);

        // Excel::queue(new DeviceTelemetryExport($deviceTelemetries), 'export/excel/invoices-1.xlsx')
        //     ->onQueue('export-telemetry-rsc')
        //     ->chain([
        //         new NotifyUserOfCompletedExport(request()->user()),
        //     ]);

        return response()->json([
            'message' => 'Export sedang berlangsung!'
        ]);
    }

    public function downloadCompletedExport() {
        return Storage::download(storage_path('app/export/excel/rsc-telemetri-'. request()->user()->id .'.xlsx'));
    }
}
