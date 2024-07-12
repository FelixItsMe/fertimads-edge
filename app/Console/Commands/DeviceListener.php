<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class DeviceListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:device-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to mqtt message response from devices';

    private function formatMode(string $mode) : string {
        switch ($mode) {
            case 'schedule/semi-auto':
                return 'schedule';
                break;
            case 'sensor':
                return 'auto';
                break;
            case 'manual':
                return 'manual';
                break;

            default:
                return 'manual';
                break;
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('MQTT START');
        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
        $mqtt = MQTT::connection();
        $mqtt->subscribe('fertimads/response', function (string $topic, string $message) {
            echo now()->format('H:i:s') . "\n";
            $data = json_decode($message);


            $device = Device::query()
                ->with('deviceSelenoids')
                ->firstWhere('series', $data->ID);
            $this->info($device->series);

            if ($device && isset($data->statusLahan)) {
                $this->info('Device found');
                foreach ($data->statusLahan as $kGarden => $mode) {
                    $formatedMode = $this->formatMode($mode);
                    switch ($kGarden) {
                        case 'L1':
                            $selenoidId = 1;
                            break;
                        case 'L2':
                            $selenoidId = 2;
                            break;
                        case 'L3':
                            $selenoidId = 3;
                            break;
                        case 'L4':
                            $selenoidId = 4;
                            break;

                        default:
                            # code...
                            break;
                    }

                    $selenoid = collect($device->deviceSelenoids)
                        ->firstWhere('selenoid', $selenoidId);

                    if ($selenoid && $selenoid->current_mode != $formatedMode) {
                        $selenoid->current_mode = $formatedMode;
                    }
                }

                $device->push();
            }
        }, 1);
        $mqtt->loop(true);
    }
}
