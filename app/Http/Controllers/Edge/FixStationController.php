<?php

namespace App\Http\Controllers\Edge;

use App\Http\Controllers\Controller;
use App\Http\Requests\Edge\FixStation\StoreFixStationTelemetriesRequest;
use App\Jobs\StoreFixStationJob;
use App\Models\FixStation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FixStationController extends Controller
{
    public function index(): View
    {
        abort_if((!config('edge.status')), 403, 'Tidak dapat mengakses konten ini!');

        $fixStations = FixStation::query()
            ->latest()
            ->paginate(10);

        $lastExported = $this->lastExported();

        return view('pages.edge.fix-station.index', compact('fixStations', 'lastExported'));
    }

    public function getTelemetries(): JsonResponse
    {
        $fixStations = FixStation::query()
            ->latest()
            ->limit(10)
            ->get();

        return response()
            ->json($fixStations);
    }

    public function getLastExportTelemetry(): JsonResponse
    {
        $fixStationLastExported = $this->lastExported();

        return response()
            ->json($fixStationLastExported);
    }

    public function storeTelemetries(StoreFixStationTelemetriesRequest $request)
    {
        StoreFixStationJob::dispatch()->onQueue('fix-station');

        return back()
            ->with('success', 'Export sedang diproses. Cek Log laravel jika data tidak terkirim ke cloud!');
    }

    private function lastExported()
    {
        return FixStation::query()
            ->where('is_last_exported', 1)
            ->latest()
            ->first();
    }
}
