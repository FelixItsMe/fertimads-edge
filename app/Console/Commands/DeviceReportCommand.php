<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceReport;
use App\Models\DeviceScheduleExecute;
use App\Models\DeviceScheduleRun;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use PhpMqtt\Client\Facades\MQTT;

class DeviceReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:device-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MQTT subscribe to fertimads/reports to listen to device report from pump/selenoid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('MQTT START');
        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
        $mqtt = MQTT::connection();
        $mqtt->subscribe('fertimads/reports', function (string $topic, string $message) {
            $data = json_decode($message);

            $device = Device::query()
                ->with('deviceSelenoids.activeDeviceSchedule')
                ->firstWhere('series', $data->ID);

            if ($device && isset($data->Reports)) {
                $this->info('Device found');

                $report = $data->Reports;

                $selenoids = collect($device->deviceSelenoids)
                    ->firstWhere('selenoid', $report->lahanID);

                if ($selenoids) {
                    $this->info('Yes');
                }

                $utcSeven = 7;

                DeviceReport::insert([
                    'device_selenoid_id' => $selenoids['id'],
                    'mode' => $report->mode,
                    'type' => $report->tipe,
                    'by_sensor' => $report->bySensor,
                    'total_time' => $report->totalTime,
                    'total_volume' => $report->totalVolume,
                    'start_time' => now()->parse($report->beginTime),
                    'end_time' => now()->parse($report->endTime),
                    'created_at' => now(),
                ]);

                if ($selenoids['activeDeviceSchedule']) {
                    $deviceScheduleRun = DeviceScheduleExecute::query()
                        ->whereHas('deviceScheduleRun', function(Builder $query)use($selenoids){
                            $query->where('device_schedule_id', $selenoids['activeDeviceSchedule']['id']);
                        })
                        ->whereNull('end_time')
                        ->orderBy('start_time')
                        ->first();

                    if ($deviceScheduleRun) {
                        $deviceScheduleRun->end_time = now()->parse($report->endTime);
                        $deviceScheduleRun->total_volume = $report->totalVolume;
                        $deviceScheduleRun->save();
                    }
                }

                $fertilizerSchedule = DeviceFertilizerSchedule::query()
                    ->with('scheduleExecute')
                    ->whereDate('execute_start', now()->parse($report->beginTime)->format('Y-m-d'))
                    ->where('device_selenoid_id', $selenoids['id'])
                    ->where('is_finished', 0)
                    ->orderBy('execute_start')
                    ->first();

                if ($fertilizerSchedule) {
                    $fertilizerSchedule->is_finished = 1;
                    $fertilizerSchedule->scheduleExecute->execute_end = now()->parse($report->endTime);
                    $fertilizerSchedule->scheduleExecute->total_volume = $report->totalVolume;
                    $fertilizerSchedule->push();
                }
            }
        }, 1);
        $mqtt->loop(true);
    }
}
