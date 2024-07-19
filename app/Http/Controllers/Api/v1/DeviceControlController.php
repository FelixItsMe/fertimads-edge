<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\GardenSelenoidModeEnums;
use App\Enums\GardenSelenoidStatusEnums;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\DeviceControl\StoreCancelDeviceSchedule;
use App\Http\Requests\Api\v1\DeviceControl\StoreDeviceManualRequest;
use App\Http\Requests\Api\v1\DeviceControl\StoreDeviceScheduleRequest;
use App\Http\Requests\Api\v1\DeviceControl\StoreDeviceSemiAutoRequest;
use App\Http\Requests\Api\v1\DeviceControl\StoreDeviceSensorRequest;
use App\Models\Commodity;
use App\Models\Device;
use App\Models\DeviceSchedule;
use App\Models\DeviceSelenoid;
use App\Models\DeviceSensor;
use App\Models\Garden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\Facades\MQTT;

class DeviceControlController extends Controller
{
    public function index(Request $request, Device $device): JsonResponse
    {
        $gardenId = $request->query('garden_id');
        $selenoid = DeviceSelenoid::query()
            ->where([
                ['device_id', $device->id],
                ['garden_id', $gardenId],
            ])
            ->first();
        $schedule = DeviceSchedule::query()
            ->with(['deviceSelenoid:id,device_id,garden_id,selenoid'])
            ->whereHas('deviceSelenoid', function(Builder $query)use($device, $gardenId){
                $query->where([
                        ['device_id', $device->id],
                        ['garden_id', $gardenId],
                    ]);
            })
            ->active()
            ->first();

        $sensor = DeviceSensor::query()
            ->with(['deviceSelenoid:id,device_id,garden_id,selenoid'])
            ->whereHas('deviceSelenoid', function(Builder $query)use($device, $gardenId){
                $query->where([
                        ['device_id', $device->id],
                        ['garden_id', $gardenId],
                    ]);
            })
            ->first();

        return response()->json([
            'message'   => 'Controll Data',
            'selenoid' => $selenoid,
            'schedule'  => $schedule,
            'auto'      => $sensor,
        ]);
    }

    public function storeDeviceSensor(StoreDeviceSensorRequest $request, Device $device) : JsonResponse
    {
        $garden = Garden::query()
            ->with('deviceSelenoid.deviceSensor')
            ->whereHas('deviceSelenoid', function($query)use($device){
                $query->where('device_id', $device->id);
            })
            ->findOrFail($request->safe()->garden_id);

        if ($garden->deviceSelenoid->deviceSensor) {
            $garden->deviceSelenoid->deviceSensor->sensors = $request->safe()->except('garden_id');
            $garden->push();
        } else {
            DeviceSensor::create([
                'device_selenoid_id' => $garden->deviceSelenoid->id,
                'sensors'   => $request->safe()->except('garden_id'),
            ]);
        }

        $deviceSensors = DeviceSensor::query()
            ->with([
                'deviceSelenoid'
            ])
            ->whereHas('deviceSelenoid', fn($query) => $query->where('device_id', $device->id))
            ->get()
            ->map(function(DeviceSensor $deviceSensor, $key){
                $i = 0;
                return [
                    "id_lahan" => $deviceSensor->deviceSelenoid->selenoid,
                    "sensor_threshold" => collect($deviceSensor->sensors)
                        ->mapWithKeys(function($sensor, $sKey)use(&$i){
                            $formatedThreshold = [
                                $i => [
                                    "Sensor" => ucfirst($sKey),
                                    "Enable" => $sensor["enable"],
                                    "UpLimit" => $sensor["upper_limit"],
                                    "LowLimit" => $sensor["lower_limit"],
                                ]
                            ];
                            $i++;
                            return $formatedThreshold;
                        })
                ];
            });

        MQTT::publish(
            'fertimads/' . $device->series,
            json_encode([
                'mode' => 'sensor',
                'setting_lahan' => $deviceSensors,
            ])
        );

        return response()->json([
            'message' => 'Command Send, check the device!',
        ]);
    }

    private function formatStatus(int $status) : string {
        switch ($status) {
            case 0:
                return 'off';
                break;
            case 1:
                return 'on';
                break;

            default:
                return 'off';
                break;
        }
    }

    public function indexDeviceManual(Device $device) : JsonResponse {
        $device->load('deviceSelenoids');

        return response()->json([
            'message' => 'Current Device Selenoid Status',
            'selenoids' => $device->deviceSelenoids
        ]);
    }

    public function storeDeviceManual(StoreDeviceManualRequest $request, Device $device) : JsonResponse {
        $manual_selenoid_status = DeviceSelenoid::query()
            ->where([
                ['device_id', $device->id],
            ])
            ->get(['id', 'selenoid', 'status', 'garden_id']);

        $id_land = collect([1,2,3,4]);
        $exist_selenoid = collect();

        $formated = $manual_selenoid_status->mapWithKeys(function(DeviceSelenoid $deviceSelenoid, int $key)use(&$exist_selenoid, $request){
            $exist_selenoid->push($deviceSelenoid->selenoid);
            $status = $deviceSelenoid->garden_id == $request->safe()->garden_id
                ? $this->formatStatus($request->safe()->status)
                : $deviceSelenoid->status->getLabelText();
            return ["lahan" . $deviceSelenoid->selenoid => $status];
        });

        $diff = $id_land
            ->diff($exist_selenoid->all())
            ->mapWithKeys(function(int $selenoid, int $key){
                return ["lahan" . $selenoid => GardenSelenoidStatusEnums::OFF->getLabelText()];
            });

        MQTT::publish(
            'fertimads/' . $device->series,
            json_encode(
                $formated
                    ->merge($diff->all())
                    ->merge([
                        'mode' => 'manual',
                        'tipe' => $request->safe()->type,
                    ])
                    ->all()
            )
        );

        return response()->json([
            'message' => 'Status Send',
        ]);
    }

    public function storeDeviceSemiAuto(StoreDeviceSemiAutoRequest $request, Device $device) : JsonResponse {
        $garden = Garden::query()
            ->whereHas('deviceSelenoid', fn($query) => $query->where('device_id', $device->id))
            ->findOrFail($request->safe()->garden_id);
        $formated = [
            'idLahan'   => $garden->deviceSelenoid->selenoid,
            'tipe'      => $request->safe()->type,
            'vol'       => $request->safe()->volume,
        ];

        MQTT::publish(
            'fertimads/' . $device->series,
            json_encode([
                'mode' => 'schedule/semi-auto',
                'lahan' => [$formated],
            ])
        );

        return response()->json([
            'message' => 'Command Send',
        ]);
    }

    public function storeDeviceSchedule(StoreDeviceScheduleRequest $request, Device $device) : JsonResponse {
        $garden = Garden::query()
            ->with([
                'deviceSelenoid' => function($query)use($device){
                    $query->where('device_id', $device->id);
                },
                'deviceSelenoid.activeDeviceSchedule'
            ])
            ->find($request->safe()->garden_id);

        if ($garden->deviceSelenoid->activeDeviceSchedule) {
            return response()->json([
                'message' => 'Kebun sudah memiliki penjadwalan yang sedang berjalan! Hapus jadwal sebelumnya sebelum membuat yang baru!'
            ], 400);
        }

        $commodity = Commodity::query()
            ->with('lastCommodityPhase')
            ->find($garden->commodity_id);

        if (!$commodity->lastCommodityPhase) {
            return response()->json([
                'message' => 'Komoditi belum memiliki data fase! Harap isi sebelum melanjutkan pembuatan jadwal.'
            ], 400);
        }

        $commodityAge = $request->safe()->commodity_age;

        $startDate = now()->parse($request->safe()->start_date);
        $plantedDate = $startDate->copy()->subDays($commodityAge);
        $remainingDays = $commodity->lastCommodityPhase->age - $commodityAge;
        $endDate = $startDate->copy()->addDays($remainingDays);

        DeviceSchedule::create([
            'device_selenoid_id'    => $garden->deviceSelenoid->id,
            'commodity_id'          => $commodity->id,
            'type'                  => $request->safe()->type,
            'commodity_age'         => $commodityAge,
            'start_date'            => $startDate,
            'end_date'              => $endDate,
            'execute_time'          => $request->safe()->execute_time,
        ]);

        $garden->deviceSelenoid->current_mode = 'schedule';
        $garden->push();

        return response()->json([
            'message' => 'Schedule Saved',
            'details' => [
                'plantedDate' => $plantedDate,
                'startDate'     => $startDate->format('Y-m-d'),
                'commodityAge' => $commodityAge,
                'remainingDays' => $remainingDays,
                'endDate'     => $endDate->format('Y-m-d'),
                'executeTime'     => $request->safe()->execute_time,
            ]
        ]);
    }

    public function updateCancelDeviceSchedule(StoreCancelDeviceSchedule $request, Device $device) : JsonResponse {
        $garden = Garden::query()
            ->with([
                'deviceSelenoid' => function($query)use($device){
                    $query->where('device_id', $device->id);
                },
            ])
            ->has('deviceSelenoid')
            ->find($request->safe()->garden_id);

        DeviceSchedule::query()
            ->where('device_selenoid_id', $garden->deviceSelenoid->id)
            ->active()
            ->update([
                'is_finished' => 1
            ]);

        return response()->json([
            'message' => 'Schedule canceled'
        ]);
    }

    public function testSchedule() : JsonResponse {
        // $now = now()->parse('15:45')->startOfMinute();
        // [$formatedDate, $formatedTime] = explode(' ', $now->copy()->format('Y-m-d H:i:s'));
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

        // if (!$deviceSchedules) {
        //     return false;
        // }

        // $idLands = $deviceSchedules->map(function($schedule, $key){
        //     return $schedule->deviceSelenoid->garden->land;
        // })
        // ->unique('id')
        // ->all();

        // $newLands = collect();

        // foreach ($idLands as $land) {
        //     $response = Http::get('https://api.open-meteo.com/v1/forecast?latitude='.$land->latitude.'&longitude='.$land->longitude.'&hourly=et0_fao_evapotranspiration&timezone=Asia%2FBangkok&forecast_days=1');

        //     if ($response->ok()) {
        //         $newLands->push([
        //             'id' => $land->id,
        //             'meteo' => $response->json()
        //         ]);
        //     }
        // }


        // $grouped = $deviceSchedules->groupBy(function ($item, int $key) {
        //     return $item->deviceSelenoid->device->series;
        // });

        // foreach ($grouped->all() as $device_series => $values) {
        //     $formatedMessages = collect();
        //     foreach ($values as $value) {
        //         $land = $newLands->firstWhere('id', $value->garden->land_id);
        //         if ($land) {
        //             $startDate = now()->parse($value->start_date);
        //             $plantedDate = $startDate->copy()->subDays($value->commodity_age)->startOfDay();
        //             $currentAge = $plantedDate->diffInDays($now->copy()->startOfDay());
        //             $phase = collect($value->commodity->commodityPhases)
        //                 ->first(function($phase)use($currentAge){
        //                     return $currentAge <= $phase->age;
        //                 });
        //             $indexEt = collect($land['meteo']['hourly']['time'])
        //                 ->search($formatedDate ."T" . $now->copy()->startOfHour()->format('H:i'));
        //             $et0 = $indexEt !== false
        //                 ? $land['meteo']['hourly']['et0_fao_evapotranspiration'][$indexEt]
        //                 : null;
        //             $etDay = $et0 * $phase->kc;
        //             $irigasi = $etDay * $value->garden->area;
        //             $formatedMessages->push([
        //                 'idLahan' => $value->garden->deviceSelenoid->selenoid,
        //                 'tipe' => $this->formatedType($value->type),
        //                 'vol' => $irigasi
        //             ]);
        //         }
        //     }

        //     $mqtt = MQTT::connection();
        //     $mqtt->publish(
        //         'fertimads/' . $device_series,
        //         json_encode([
        //             'mode' => 'schedule/semi-auto',
        //             'lahan' => $formatedMessages->all()
        //         ])
        //     );
        //     $mqtt->disconnect();
        // }

        $validator = Validator::make([
            'date' => '2024-07-12',
            'time' => '',
        ], [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
        ]);

        return response()->json([
            'deviceSchedules' => isset(collect($validator->errors())->all()['time'])
        ]);
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
