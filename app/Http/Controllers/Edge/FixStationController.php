<?php

namespace App\Http\Controllers\Edge;

use App\Http\Controllers\Controller;
use App\Http\Requests\Edge\FixStation\StoreFixStationTelemetriesRequest;
use App\Models\FixStation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FixStationController extends Controller
{
    public function index() : View {
        abort_if((!config('edge.status')), 403, 'Tidak dapat mengakses konten ini!');

        $fixStations = FixStation::query()
            ->latest()
            ->paginate(10);

        return view('pages.edge.fix-station.index', compact('fixStations'));
    }

    public function getTelemetries() : JsonResponse {
        $fixStations = FixStation::query()
            ->latest()
            ->limit(10)
            ->get();

        return response()
            ->json($fixStations);
    }

    public function storeTelemetries(StoreFixStationTelemetriesRequest $request) {
        $fixStationLastExported = FixStation::query()
            ->where('is_last_exported', 1)
            ->latest()
            ->first();

        $fixStationTelemetries = [];
        DB::table('fix_stations')
            ->select(['garden_id', 'samples', 'created_at'])
            ->when($fixStationLastExported->created_at, function($query)use($fixStationLastExported){
                $query->where('created_at', '>', $fixStationLastExported->created_at);
            })
            ->latest()
            ->chunk(100, function(Collection $fix_stations)use(&$fixStationTelemetries){
                $fixStationTelemetries[] = $fix_stations->map(function($fixStation){
                    return [
                        'garden_id' => $fixStation->garden_id,
                        'created_at' => $fixStation->created_at,
                        'samples' => json_decode($fixStation->samples),
                    ];
                })->all();
            });

        if (count($fixStationTelemetries) === 0) {
            return redirect()
                ->back()
                ->with('failed', 'Data tidak ada');
        }

        // dd(collect($fixStationTelemetries)->flatten(1)->toArray());
        $response = Http::withHeaders([
            'Accept' => "application/json",
            'Content-Type' => "application/json",
            'X-Fertimads-Edge' => config('edge.token'),
        ])->post(config('edge.cloud_url'), [
            'data' => collect($fixStationTelemetries)->flatten(1)->toArray(),
        ]);

        $response->throw();

        $fixStation = FixStation::query()
            ->latest()
            ->first();

        if ($fixStation) {
            $fixStation->is_last_exported = 1;
            $fixStation->save();
        }


        return back()
            ->with('success', 'Export sedang diproses');
    }
}
