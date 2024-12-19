<?php

namespace App\Http\Controllers\Edge;

use App\Http\Controllers\Controller;
use App\Models\FixStation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FixStationController extends Controller
{
    public function index() : View {
        abort_if(config('app.type') !== 'edge', 403, 'Tidak dapat mengakses konten ini!');

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
}
