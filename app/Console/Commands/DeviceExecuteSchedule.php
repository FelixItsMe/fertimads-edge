<?php

namespace App\Console\Commands;

use App\Jobs\SendScheduledCommand;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceSchedule;
use App\Models\DeviceScheduleRun;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use PhpMqtt\Client\Facades\MQTT;

class DeviceExecuteSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:device-execute-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // NOTE: Delete the parse time
        $now = now()->parse('2024-07-24 15:00:00')->startOfMinute();
        [$formatedDate, $formatedTime] = explode(' ', $now->copy()->format('Y-m-d H:i:s'));
        // $deviceSchedules = DeviceSchedule::query()
        //     ->with([
        //         'deviceSelenoid:id,device_id,garden_id,selenoid',
        //         'deviceSelenoid.device:id,series',
        //         'deviceSelenoid.garden:id,land_id,area',
        //         'deviceSelenoid.garden.land:id,latitude,longitude',
        //         'commodity:id',
        //         'commodity.commodityPhases:id,commodity_id,age,kc',
        //     ])
        //     ->has('deviceSelenoid.garden')
        //     ->active()
        //     ->where([
        //         ['start_date', '<=', $formatedDate],
        //         ['end_date', '>=', $formatedDate],
        //     ])
        //     ->whereTime('execute_time', '=', $formatedTime)
        //     ->get();

        $deviceScheduleRuns = DeviceScheduleRun::query()
            ->with([
                'deviceSchedule.deviceSelenoid:id,device_id,garden_id,selenoid',
                'deviceSchedule.deviceSelenoid.device:id,series',
            ])
            ->whereHas('deviceSchedule', function($query){
                $query->where('is_finished', 0);
            })
            ->where('start_time', $now->copy()->format('Y-m-d H:i:s'))
            ->get();
        $deviceFertilizerSchedules = DeviceFertilizerSchedule::query()
            ->with([
                'deviceSelenoid:id,device_id,garden_id,selenoid',
                'deviceSelenoid.device:id,series',
            ])
            ->has('deviceSelenoid.garden')
            ->active()
            ->where('execute_start', $now->copy()->format('Y-m-d H:i:s'))
            ->get();

        if (count($deviceScheduleRuns) > 0 || count($deviceFertilizerSchedules) > 0) {
            $this->info(count($deviceScheduleRuns) . " " . count($deviceFertilizerSchedules));
            SendScheduledCommand::dispatch($now, $deviceScheduleRuns, $deviceFertilizerSchedules)
                ->onQueue('executeScheduled');
        }
    }
}
