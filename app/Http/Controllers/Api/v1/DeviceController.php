<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\DeviceTypeEnums;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceTelemetry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function getDevices() : JsonResponse {
        $devices = Device::query()
            ->select(['id', 'device_type_id', 'series', 'image'])
            ->with('deviceType:id,name,version,image')
            ->whereHas('deviceType', function(Builder $query){
                $query->where('type', DeviceTypeEnums::HEAD_UNIT);
            })
            ->get();

        return response()->json([
            'message' => 'Data Devices',
            'devices' => $devices
        ]);
    }

    public function detailDevice(Device $device) : JsonResponse {
        $device->load(['deviceType', 'deviceSelenoids']);

        return response()->json([
            'message' => 'Detail device with type',
            'device' => $device,
        ]);
    }

    public function rscTelemetries(Request $request, Device $device, $selenoid) : JsonResponse {
        $paginate = $request->query('paginate', 1);
        $paginate = $paginate < 1 ? 1 : $paginate;
        $offset = ($paginate - 1) * 10;
        $limit = 10;
        $telemetries = DeviceTelemetry::query()
            ->where('device_id', $device->id)
            ->orderByDesc('created_at')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $formatTelemetries = collect($telemetries)
            ->map(function($telemetry, $key)use($selenoid){
                $new = (array) $telemetry->telemetry;
                $new['SS' . $selenoid]->created_at = $telemetry->created_at->format('Y-m-d H:i:s');
                $new['SS' . $selenoid]->areaH = $new['DHT1']->H;
                $new['SS' . $selenoid]->areaT = $new['DHT1']->T;
                return $new['SS' . $selenoid];
            })
            ->all();

        return response()->json([
            'message' => 'List telemetries',
            'telemetries' => $formatTelemetries,
        ]);
    }
}
