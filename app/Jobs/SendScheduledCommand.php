<?php

namespace App\Jobs;

use App\Models\DeviceFertilizeScheduleExecute;
use App\Models\DeviceSchedule;
use App\Models\DeviceScheduleExecute;
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
    public function __construct(protected string $now, protected $deviceScheduleRuns, protected $deviceFertilizerSchedules)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->processDeviceWaterSchedule($this->deviceScheduleRuns);
        $this->processDeviceFertilizerSchedule($this->deviceFertilizerSchedules);
    }

    private function processDeviceWaterSchedule($deviceScheduleRuns) : bool {
        if (!$deviceScheduleRuns) {
            return false;
        }

        $now = now()->parse($this->now)->startOfMinute();
        [$formatedDate, $formatedTime] = explode(' ', $now->copy()->format('Y-m-d H:i:s'));

        // group schedules by device series
        $grouped = $deviceScheduleRuns->groupBy(function ($item, int $key) {
            return $item->deviceSchedule->deviceSelenoid->device->series;
        });

        $deviceScheduleExecutesInsert = collect();

        foreach ($grouped->all() as $device_series => $values) {
            $formatedMessages = collect();
            foreach ($values as $value) {
                $formatedMessages->push([
                    'idLahan' => $value->deviceSchedule->deviceSelenoid->selenoid,
                    'tipe' => 'penyiraman',
                    'vol' => $value->total_volume,
                ]);
                $deviceScheduleExecutesInsert->push([
                    'device_schedule_run_id' => $value->id,
                    'start_time' => $now,
                    'total_volume' => $value->total_volume,
                    'created_at' => now(),
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

        if ($deviceScheduleExecutesInsert->count() > 0) {
            DeviceScheduleExecute::insert($deviceScheduleExecutesInsert->all());
        }

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

        $fertilizerScheduleExecute = collect();

        foreach ($grouped->all() as $device_series => $values) {
            $formatedMessages = collect();
            foreach ($values as $value) {
                $formatedMessages->push([
                    'idLahan' => $value->deviceSelenoid->selenoid,
                    'tipe' => $value->type->getDeviceLabel(),
                    'vol' => $value->total_volume,
                ]);

                $fertilizerScheduleExecute->push([
                    'device_fertilizer_schedule_id' => $value->id,
                    'type' => $value->type->value,
                    'execute_start' => $now,
                    'total_volume' => $value->total_volume,
                    'created_at' => now(),
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

        if ($fertilizerScheduleExecute->count() > 0) {
            DeviceFertilizeScheduleExecute::insert($fertilizerScheduleExecute->all());
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
