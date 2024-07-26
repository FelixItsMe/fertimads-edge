<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\DailyIrrigation;
use App\Models\DeviceSchedule;
use App\Models\DeviceScheduleRun;
use App\Models\Garden;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function calculateDailyIrrigationInGarden(DeviceSchedule $deviceSchedule, Garden $garden, Commodity $commodity, Carbon $startDate, Carbon $plantedDate, Carbon $endDate, $executeTime) : bool {
        $dailyIrrigations = DailyIrrigation::query()
            ->where([
                ['date', '>=', $startDate->format('Y-m-d')],
                ['date', '<=', $endDate->format('Y-m-d')],
            ])
            ->get();

        $deviceSchedules = collect();

        $now = now();

        foreach ($dailyIrrigations as $dailyIrrigation) {
            $plantedDate = $plantedDate->copy()->startOfDay();
            $currentAge = $plantedDate->diffInDays(now()->parse($dailyIrrigation->date)->startOfDay());
            $start = now()->parse($dailyIrrigation->date . " " . $executeTime);

            // get current phase by current age
            $currentPhase = collect($commodity->commodityPhases)
                ->first(function($phase)use($currentAge){
                    return $currentAge <= $phase->age;
                });

            // get ET0 value from prev index time $indexEt
            $et0 = $dailyIrrigation->eto;
            $etDay = $et0 * $currentPhase->kc;
            $irigasi = $etDay * $garden->area;
            $calcMinutes = (!$garden->deviceSelenoid->device->debit)
                ? 60
                : ($irigasi / $garden->deviceSelenoid->device->debit);
            $end = $start->copy()->addMinutes($calcMinutes);
            $deviceSchedules->push([
                'device_schedule_id' => $deviceSchedule->id,
                'start_time' => $start,
                'end_time' => $end,
                'total_volume' => $irigasi,
                'created_at' => $now,
            ]);
        }

        DeviceScheduleRun::insert($deviceSchedules->all());

        return true;
    }
}
