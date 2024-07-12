<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeviceTelemetry;
use App\Models\Garden;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTelemetryController extends Controller
{
    public function gardenLatestTelemetry(Garden $garden) : JsonResponse {
        $garden->load('deviceSelenoid');

        if (!$garden->deviceSelenoid) {
            return response()->json([
                'message' => 'Latest telemetry',
                'telemetry' => null
            ]);
        }

        $latestTelemetry = DeviceTelemetry::query()
            ->where('device_id', $garden->deviceSelenoid->device_id)
            ->orderByDesc('created_at')
            ->first();

        return response()
            ->json([
                'message' => 'Latest Telemetry Data',
                'telemetry' => $latestTelemetry
            ]);
    }
}
