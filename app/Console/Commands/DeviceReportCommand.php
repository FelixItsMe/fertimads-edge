<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\DeviceReport;
use App\Models\DeviceScheduleRun;
use Illuminate\Console\Command;
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
                    $deviceScheduleRun = DeviceScheduleRun::query()
                        ->where('device_schedule_id', $selenoids['activeDeviceSchedule']['id'])
                        ->whereNull('end_time')
                        ->orderByDesc('created_at')
                        ->first();

                    if ($deviceScheduleRun) {
                        $deviceScheduleRun->end_time = now()->parse($report->endTime)->addHours(7);
                        $deviceScheduleRun->save();
                    }
                }
            }
        }, 1);
        $mqtt->loop(true);
    }
}
