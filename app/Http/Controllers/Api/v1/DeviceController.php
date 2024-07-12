<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function getDevices() : JsonResponse {
        $devices = Device::query()
            ->select(['id', 'device_type_id', 'series', 'image'])
            ->with('deviceType:id,name,version,image')
            ->get();

        return response()->json([
            'message' => 'Data Devices',
            'devices' => $devices
        ]);
    }

    public function detailDevice(Device $device) : JsonResponse {
        $device->load('deviceType');

        return response()->json([
            'message' => 'Detail device with type',
            'device' => $device,
        ]);
    }
}
