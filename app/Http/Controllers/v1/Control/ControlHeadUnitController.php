<?php

namespace App\Http\Controllers\v1\Control;

use App\Enums\GardenSelenoidStatusEnums;
use App\Http\Controllers\Controller;
use App\Http\Requests\Control\StoreControlManualRequest;
use App\Http\Requests\Control\StoreControlScheduleFertilizerRequest;
use App\Http\Requests\Control\StoreControlScheduleWaterRequest;
use App\Http\Requests\Control\StoreControlSemiAutoRequest;
use App\Http\Requests\Control\StoreControlSensorRequest;
use App\Models\Commodity;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceSchedule;
use App\Models\DeviceSelenoid;
use App\Models\DeviceSensor;
use App\Models\Garden;
use App\Models\Land;
use App\Services\ScheduleService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;

class ControlHeadUnitController extends Controller
{
    public function __construct(private ScheduleService $scheduleService)
    {
    }

    public function indexControlManual() : View {
        $lands = Land::query()
            ->has('gardens.deviceSelenoid')
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('pages.control.head-unit.manual', compact('lands'));
    }

    public function storeControlManual(StoreControlManualRequest $request) : JsonResponse {
        $garden = Garden::query()
            ->with('deviceSelenoid.device:id,series')
            ->findOrFail($request->safe()->garden_id);

        $manual_selenoid_status = DeviceSelenoid::query()
            ->where([
                ['device_id', $garden->deviceSelenoid->device_id],
            ])
            ->get(['id', 'selenoid', 'status', 'garden_id']);

        $id_land = collect([1,2,3,4]);
        $exist_selenoid = collect();

        $formated = $manual_selenoid_status->mapWithKeys(function(DeviceSelenoid $deviceSelenoid, int $key)use(&$exist_selenoid, $request){
            $exist_selenoid->push($deviceSelenoid->selenoid);
            $status = $deviceSelenoid->garden_id == $request->safe()->garden_id
                ? $request->safe()->status
                : $deviceSelenoid->status->getLabelText();
            return ["lahan" . $deviceSelenoid->selenoid => $status];
        });

        $diff = $id_land
            ->diff($exist_selenoid->all())
            ->mapWithKeys(function(int $selenoid, int $key){
                return ["lahan" . $selenoid => GardenSelenoidStatusEnums::OFF->getLabelText()];
            });

        MQTT::publish(
            'fertimads/' . $garden->deviceSelenoid->device->series,
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
            'message' => 'Manual store',
            'request' => $request->validated()
        ]);
    }

    public function indexControlSemiAuto() : View {
        $lands = Land::query()
            ->has('gardens.deviceSelenoid')
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('pages.control.head-unit.semi-auto', compact('lands'));
    }

    public function storeControlSemiAuto(StoreControlSemiAutoRequest $request) : JsonResponse {
        $garden = Garden::query()
            ->with([
                'deviceSelenoid:id,garden_id,device_id,selenoid',
                'deviceSelenoid.device:id,series',
            ])
            ->has('deviceSelenoid')
            ->findOrFail($request->safe()->garden_id);

        $formated = [
            'idLahan'   => $garden->deviceSelenoid->selenoid,
            'tipe'      => $request->safe()->type,
            'vol'       => $request->safe()->volume,
        ];

        MQTT::publish(
            'fertimads/' . $garden->deviceSelenoid->device->series,
            json_encode([
                'mode' => 'schedule/semi-auto',
                'lahan' => [$formated],
            ])
        );

        return response()->json([
            'message' => 'Command Send',
        ]);
    }

    public function indexControlSensor() : View {
        $lands = Land::query()
            ->has('gardens.deviceSelenoid')
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('pages.control.head-unit.sensor', compact('lands'));
    }

    public function storeControlSensor(StoreControlSensorRequest $request) : JsonResponse {

        $garden = Garden::query()
            ->with([
                'deviceSelenoid:id,garden_id,device_id,selenoid',
                'deviceSelenoid.device:id,series',
                'deviceSelenoid.deviceSensor',
            ])
            ->has('deviceSelenoid')
            ->findOrFail($request->safe()->garden_id);

        $sensors = collect($request->safe()->except('garden_id'))
            ->map(function($sensor, $sKey){
                return [
                    "enable" => $sensor["enable"],
                    "upper_limit" => is_null($sensor["upper_limit"]) ? null : (float) $sensor["upper_limit"],
                    "lower_limit" => is_null($sensor["lower_limit"]) ? null : (float) $sensor["lower_limit"],
                ];
            })
            ->all();

        if ($garden->deviceSelenoid->deviceSensor) {
            $garden->deviceSelenoid->deviceSensor->sensors = $sensors;
            $garden->push();
        } else {
            DeviceSensor::create([
                'device_selenoid_id' => $garden->deviceSelenoid->id,
                'sensors'   => $sensors,
            ]);
        }

        $device = $garden->deviceSelenoid->device;

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
            'message' => $request->validated(),
        ]);
    }

    public function indexControlScheduleWater() : View {
        $lands = Land::query()
            ->has('gardens.deviceSelenoid')
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('pages.control.head-unit.schedule-water', compact('lands'));
    }

    public function storeControlScheduleWater(StoreControlScheduleWaterRequest $request) : JsonResponse {
        $garden = Garden::query()
            ->with([
                'deviceSelenoid',
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

        $deviceSchedule = DeviceSchedule::create([
            'device_selenoid_id' => $garden->deviceSelenoid->selenoid,
            'garden_id' => $garden->id,
            'commodity_id' => $commodity->id,
            'commodity_age' => $commodityAge,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'execute_time' => $request->safe()->execute_time,
        ]);

        $this->scheduleService->calculateDailyIrrigationInGarden(
            $deviceSchedule,
            $garden,
            $commodity,
            $startDate,
            $plantedDate,
            $endDate,
            $request->safe()->execute_time,
        );

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

    public function indexControlScheduleFertilizer() : View {
        $lands = Land::query()
            ->has('gardens.deviceSelenoid')
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('pages.control.head-unit.schedule-fertilizer', compact('lands'));
    }

    public function storeControlScheduleFertilizer(StoreControlScheduleFertilizerRequest $request) : JsonResponse {
        $garden = Garden::query()
            ->with([
                'deviceSelenoid.device:id,series,debit'
            ])
            ->has('deviceSelenoid')
            ->find($request->safe()->garden_id);

        $start = now()->parse($request->safe()->execute_date . " " . $request->safe()->execute_time)->startOfMinute();
        $volume = $request->safe()->volume;
        $calcMinutes = (!$garden->deviceSelenoid->device->debit)
            ? 60
            : ($volume / $garden->deviceSelenoid->device->debit);
        $end = $start->copy()->addMinutes($calcMinutes);

        $activeFertilizeDevice = DeviceFertilizerSchedule::query()
            ->where(function(Builder $query)use($start){
                $query->where('execute_start', '<=', $start->format('Y-m-d H:i:s'))
                ->where('execute_end', '>=', $start->format('Y-m-d H:i:s'));
            })
            ->orWhere(function(Builder $query)use($end){
                $query->where('execute_start', '<=', $end->format('Y-m-d H:i:s'))
                ->where('execute_end', '>=', $end->format('Y-m-d H:i:s'));
            })
            ->active()
            ->count();

        if ($activeFertilizeDevice > 0) {
            return response()->json([
                'message' => 'Clash in schedule'
            ], 400);
        }

        DeviceFertilizerSchedule::create([
            'device_selenoid_id' => $garden->deviceSelenoid->id,
            'type' => $request->safe()->type,
            'execute_start' => $start,
            'execute_end' => $end,
            'total_volume' => $volume,
        ]);

        return response()->json([
            'message' => 'Berhasil menyimpan jadwal',
            'request' => $request->validated()
        ]);
    }
}
