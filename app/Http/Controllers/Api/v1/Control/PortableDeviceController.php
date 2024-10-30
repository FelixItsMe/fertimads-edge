<?php

namespace App\Http\Controllers\Api\v1\Control;

use App\Enums\DeviceTypeEnums;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\PortableDevice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortableDeviceController extends Controller
{
    public function checkSeriesDevice(Request $request) : JsonResponse {
        $request->validate([
            'device_id' => 'required|string|max:255',
        ]);

        $portableDevice = Device::query()
            ->whereHas('deviceType', function(Builder $query){
                $query->where('type', DeviceTypeEnums::PORTABLE);
            })
            ->firstWhere('series', $request->device_id);

        if (!$portableDevice) {
            return response()
                ->json([
                    'message' => 'Perangkat tidak ditemukan!'
                ], 404);
        }

        return response()
            ->json([
                'message' => 'Perangkat ditemukan!',
                'portableDevice' => $portableDevice,
            ]);
    }
}
