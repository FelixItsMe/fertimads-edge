<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\DeviceTelemetry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\Facades\MQTT;

class DeviceSensorTelemetry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:device-sensor-telemetry';

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
        $this->info('MQTT START');
        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
        $mqtt = MQTT::connection();
        $mqtt->subscribe('fertimads/sensors', function (string $topic, string $message) {
            $data = json_decode($message);

            $device = Device::query()
                ->with('deviceSelenoids')
                ->firstWhere('series', $data->ID);

            if ($device && isset($data->statusPerangkat)) {
                $this->proccessOutput($device, $data->statusPerangkat->output);

                $this->proccessInput($device, $data->statusPerangkat);

                $device->push();

                $this->info('Data Saved');
            }
        }, 1);
        $mqtt->loop(true);
    }

    private function formatStatus(string $status) : int {
        switch ($status) {
            case 'on':
                return 1;
                break;
            case 'off':
                return 0;
                break;

            default:
                return 0;
                break;
        }
    }

    private function proccessOutput(Device $device, $output) : bool {
        if(!isset($output)) return false;

        foreach ($output as $key => $status) {
            switch ($key) {
                case 'VL1':
                    $selenoidId = 1;
                    break;
                case 'VL2':
                    $selenoidId = 2;
                    break;
                case 'VL3':
                    $selenoidId = 3;
                    break;
                case 'VL4':
                    $selenoidId = 4;
                    break;

                default:
                    continue 2;
                    break;
            }
            $formatedStatus = $this->formatStatus($status);

            $selenoid = collect($device->deviceSelenoids)
                ->firstWhere('selenoid', $selenoidId);

            if ($selenoid && $selenoid->status != $formatedStatus) {
                $selenoid->status = $formatedStatus;
            }
        }

        return true;
    }

    private function proccessInput(Device $device, $statusPerangkat) : bool {
        if(!isset($statusPerangkat)) return false;

        if(!isset($statusPerangkat->input)) return false;

        $date = now()->format('Y-m-d');
        $time = now()->format('H:i:s');

        if ($statusPerangkat->dateTime) {
            $validator = Validator::make([
                'date' => $statusPerangkat->dateTime->date,
                'time' => $statusPerangkat->dateTime->time,
            ], [
                'date' => 'required|date_format:d/m/Y',
                'time' => 'required|date_format:H:i:s',
            ]);

            if (!isset(collect($validator->errors())->all()['date'])) {
                $date = now()->parse($statusPerangkat->dateTime->date)->format('Y-m-d');
            }
            if (!isset(collect($validator->errors())->all()['time'])) {
                $time = now()->parse($statusPerangkat->dateTime->time)->format('H:i:s');
            }
        }

        DeviceTelemetry::insert([
            'device_id'     => $device->id,
            'telemetry'     => json_encode($statusPerangkat->input),
            'created_at'    => now()->parse($date . ' ' . $time),
        ]);

        return true;
    }
}
