<?php

namespace App\Http\Controllers\Api\v1\Control;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\PortableDevice;
use App\Models\SmsGarden;
use App\Models\SmsTelemetry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmsGardenTelemetryController extends Controller
{
    public function store(Request $request) : JsonResponse {
        $validated = $request->validate([
            'device_id' => 'required|string|max:255',
            'garden_id' => 'required|integer|min:0|exists:gardens,id',
            'samples' => 'required|array|min:1',
            'samples.*.latitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'samples.*.longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'samples.*.ambient_humidity' => ['required', 'numeric'],
            'samples.*.ambient_temperature' => ['required', 'numeric'],
            'samples.*.n' => ['required', 'numeric'],
            'samples.*.p' => ['required', 'numeric'],
            'samples.*.k' => ['required', 'numeric'],
            'samples.*.ec' => ['required', 'numeric'],
            'samples.*.ph' => ['required', 'numeric'],
            'samples.*.soil_temperature' => ['required', 'numeric'],
            'samples.*.soil_moisture' => ['required', 'numeric'],
        ]);

        $device = PortableDevice::query()
            ->where('series', $validated['device_id'])
            ->firstOrFail();

        $now = now();

        $sms_garden = SmsGarden::create([
            'portable_device_id' => $device->id,
            'garden_id' => $validated['garden_id'],
        ]);

        $insert_telemetries = collect($validated['samples'])
            ->map(function($sample, $key)use($now, $sms_garden){
                return [
                    'sms_garden_id' => $sms_garden->id,
                    "latitude" => $sample["latitude"],
                    "longitude" => $sample["longitude"],
                    'samples' => json_encode((object) [
                        "ambient_humidity" => $sample["ambient_humidity"],
                        "ambient_temperature" => $sample["ambient_temperature"],
                        "n" => $sample["n"],
                        "p" => $sample["p"],
                        "k" => $sample["k"],
                        "soil_temperature" => $sample["soil_temperature"],
                        "soil_moisture" => $sample["soil_moisture"],
                        "ec" => $sample["ec"],
                        "ph" => $sample["ph"],
                    ]),
                    'created_at' => $now
                ];
            })
            ->all();

        SmsTelemetry::insert($insert_telemetries);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ]);
    }
}
