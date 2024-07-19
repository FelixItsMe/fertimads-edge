<?php

namespace App\Jobs;

use App\Models\DeviceSchedule;
use App\Models\DeviceScheduleRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use PhpMqtt\Client\Facades\MQTT;

class SendScheduledCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $now, protected $deviceSchedules, protected $deviceFertilizerSchedules)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->processDeviceWaterSchedule($this->deviceSchedules);
        $this->processDeviceFertilizerSchedule($this->deviceFertilizerSchedules);
    }

    private function processDeviceWaterSchedule($deviceSchedules) : bool {
        if (!$deviceSchedules) {
            return false;
        }

        $now = now()->parse($this->now)->startOfMinute();
        [$formatedDate, $formatedTime] = explode(' ', $now->copy()->format('Y-m-d H:i:s'));

        // change format and filter land id
        $idLands = $deviceSchedules->map(function($schedule, $key){
            return $schedule->deviceSelenoid->garden->land;
        })
        ->unique('id')
        ->all();

        $deviceScheduleRuns = collect();

        $newLands = collect();

        // get meteo fron lands latitude longitude
        foreach ($idLands as $land) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast?latitude='.$land->latitude.'&longitude='.$land->longitude.'&hourly=et0_fao_evapotranspiration&timezone=Asia%2FBangkok&forecast_days=1');

            if ($response->ok()) {
                $newLands->push([
                    'id' => $land->id,
                    'meteo' => $response->json()
                ]);
            }
        }

        // group schedules by device series
        $grouped = $deviceSchedules->groupBy(function ($item, int $key) {
            return $item->deviceSelenoid->device->series;
        });

        foreach ($grouped->all() as $device_series => $values) {
            $formatedMessages = collect();
            foreach ($values as $value) {
                $land = $newLands->firstWhere('id', $value->deviceSelenoid->garden->land_id);
                if ($land) {
                    $startDate = now()->parse($value->start_date);
                    $plantedDate = $startDate->copy()->subDays($value->commodity_age)->startOfDay();
                    $currentAge = $plantedDate->diffInDays($now->copy()->startOfDay());

                    // get current phase by current age
                    $currentPhase = collect($value->commodity->commodityPhases)
                        ->first(function($phase)use($currentAge){
                            return $currentAge <= $phase->age;
                        });

                    // get array index of current time from array meteo for getting ET value
                    $indexEt = collect($land['meteo']['hourly']['time'])
                        ->search($formatedDate ."T" . $now->copy()->startOfHour()->format('H:i'));

                    // get ET0 value from prev index time $indexEt
                    $et0 = $indexEt !== false
                        ? $land['meteo']['hourly']['et0_fao_evapotranspiration'][$indexEt]
                        : 0;
                    $etDay = $et0 * $currentPhase->kc;
                    $irigasi = $etDay * $value->deviceSelenoid->garden->area;
                    $formatedMessages->push([
                        'idLahan' => $value->deviceSelenoid->selenoid,
                        'tipe' => 'penyiraman',
                        'vol' => $irigasi
                    ]);
                    $deviceScheduleRuns->push([
                        'device_schedule_id' => $value->id,
                        'start_time' => $now->copy(),
                        'total_volume' => $irigasi,
                        'created_at' => $now->copy(),
                    ]);
                }
            }

            if ($formatedMessages->all()) {
                $mqtt = MQTT::connection();
                $mqtt->publish(
                    'fertimads/' . $device_series,
                    json_encode([
                        'mode' => 'schedule/semi-auto',
                        'lahan' => $formatedMessages->all()
                    ])
                );
                $mqtt->disconnect();
            }
        }

        DeviceScheduleRun::insert($deviceScheduleRuns->all());

        return true;
    }

    private function processDeviceFertilizerSchedule($deviceFertilizerSchedules) : bool {
        if(!$deviceFertilizerSchedules) return false;

        $now = now()->parse($this->now)->startOfMinute();
        [$formatedDate, $formatedTime] = explode(' ', $now->copy()->format('Y-m-d H:i:s'));

        // group schedules by device series
        $grouped = $deviceFertilizerSchedules->groupBy(function ($item, int $key) {
            return $item->deviceSelenoid->device->series;
        });

        foreach ($grouped->all() as $device_series => $values) {
            $formatedMessages = collect();
            foreach ($values as $value) {
                $formatedMessages->push([
                    'idLahan' => $value->deviceSelenoid->selenoid,
                    'tipe' => $value->type->getDeviceLabel(),
                    'vol' => $value->total_volume,
                ]);
            }

            if ($formatedMessages->all()) {
                $mqtt = MQTT::connection();
                $mqtt->publish(
                    'fertimads/' . $device_series,
                    json_encode([
                        'mode' => 'schedule/semi-auto',
                        'lahan' => $formatedMessages->all()
                    ])
                );
                $mqtt->disconnect();
            }
        }

        return true;
    }

    private function formatedType(int $type) : string {
        switch ($type) {
            case 1:
                return 'penyiraman';
                break;
            case 2:
                return 'pemupukan';
                break;

            default:
                # code...
                break;
        }
    }
}
