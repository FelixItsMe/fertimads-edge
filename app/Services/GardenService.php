<?php

namespace App\Services;

use App\Models\DeviceSelenoid;
use App\Models\DeviceTelemetry;
use App\Models\Garden;

class GardenService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAvailableSelenoid(?int $device_id) : int|null {
        if(!$device_id) return null;

        $id_land = collect([1,2,3,4]);
        $used_ids = DeviceSelenoid::query()
            ->where('device_id', $device_id)
            ->pluck('selenoid');

        return $id_land->diff($used_ids)->first();
    }

    public function updateSelenoidGardenId(int $idGarden, ?int $idDevice, bool $isNewGarden = false) : int|null {
        if ($isNewGarden && !$idDevice) {
            return false;
        }

        if ($isNewGarden && $idDevice) {
            $deviceSelenoid = DeviceSelenoid::query()
                ->where('device_id', $idDevice)
                ->whereNull('garden_id')
                ->orderBy('selenoid')
                ->first();

            $deviceSelenoid->garden_id = $idGarden;
            $deviceSelenoid->save();

            return true;
        }

        $deviceSelenoid = DeviceSelenoid::query()
            ->where('garden_id', $idGarden)
            ->orderBy('selenoid')
            ->first();

        if (!$deviceSelenoid && !$idDevice) return true;

        if ($deviceSelenoid && !$idDevice) {
            $deviceSelenoid->garden_id = null;
            $deviceSelenoid->save();

            return true;
        }
        if ($deviceSelenoid && $deviceSelenoid->device_id != $idDevice) {
            $deviceSelenoid->garden_id = null;
            $deviceSelenoid->save();
        }

        $deviceSelenoid2 = DeviceSelenoid::query()
            ->where('device_id', $idDevice)
            ->whereNull('garden_id')
            ->orderBy('selenoid')
            ->first();

        if ($deviceSelenoid2) {
            $deviceSelenoid2->garden_id = $idGarden;
            $deviceSelenoid2->save();
        }

        return true;
    }

    public function formatedLatestTelemetry(Garden $garden) : object|null {
        if (!$garden->deviceSelenoid) return null;

        $latestTelemetry = DeviceTelemetry::query()
            ->where('device_id', $garden->deviceSelenoid->device_id)
            ->orderByDesc('created_at')
            ->first();

        if(!$latestTelemetry) return null;

        $selenoid = $garden->deviceSelenoid->selenoid;
        $telemetry = collect($latestTelemetry->telemetry)->toArray();

        return (object) [
            'flow_meter' => $telemetry['FM' . $selenoid],
            'soil_sensor' => $telemetry['SS' . $selenoid],
            'dht1' => $telemetry['DHT1'],
        ];
    }
}
