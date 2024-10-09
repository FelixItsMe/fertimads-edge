<?php

namespace App\Http\Controllers\Api\v1\Management;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\SmsTelemetry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortableSmsTelemetryController extends Controller
{
    public function index(Garden $garden) : JsonResponse {
        $smsTelemetries = SmsTelemetry::query()
            ->whereHas('smsGarden', function(Builder $query)use($garden){
                $query->where('garden_id', $garden->id);
            })
            ->latest('created_at')
            ->paginate(1);

        return response()
            ->json([
                'portableSmsTelemetries' => $smsTelemetries,
            ]);
    }
}
