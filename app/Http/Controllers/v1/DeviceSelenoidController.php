<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\DeviceSelenoid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceSelenoidController extends Controller
{
    public function selenoidSensor(DeviceSelenoid $deviceSelenoid) : JsonResponse {
        $deviceSelenoid->load('deviceSensor');

        return response()->json([
            'message' => 'Device sensor from selenoid',
            'sensor' => $deviceSelenoid->deviceSensor?->sensors
        ]);
    }
}
