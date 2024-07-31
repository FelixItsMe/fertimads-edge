<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeviceSchedule;
use App\Models\DeviceTelemetry;
use App\Models\Garden;
use App\Models\Land;
use App\Services\GardenService;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GardenController extends Controller
{
    public function __construct(private GardenService $gardenService)
    {

    }
    public function detailGarden(Garden $garden) : JsonResponse {
        $garden
            ->load(['commodity:id,name,image']);
        return response()->json([
            'message' => 'Detail Garden',
            'garden' => $garden,
            'telemetry'  => $this->gardenService->formatedLatestTelemetry($garden)
        ]);
    }

    public function getListGardensFromLands(Land $land) : JsonResponse {
        $gardens = Garden::query()
            ->where('land_id', $land->id)
            ->get(['id', 'name']);

        return response()->json([
            'message' => 'Data gardens from land id for list',
            'gardens' => $gardens,
        ]);
    }

    public function gardenLatestTelemetry(Garden $garden) : JsonResponse {
        $garden->load('deviceSelenoid.device:id,series,pumps');

        $currentType = $garden->deviceSelenoid?->device?->pumps;

        return response()
            ->json([
                'message'   => 'Latest Telemetry Data',
                'telemetry'  => $this->gardenService->formatedLatestTelemetry($garden),
                'currentType' => $currentType,
            ]);
    }

    public function gardenListTelemetries(Garden $garden) : JsonResponse {
        $garden->load('deviceSelenoid');

        if (!$garden->deviceSelenoid) {
            return response()->json([
                'message' => 'Latest telemetry',
                'telemetries' => []
            ]);
        }

        $telemetries = DeviceTelemetry::query()
            ->where('device_id', $garden->deviceSelenoid->device_id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()
            ->json([
                'message'   => 'Latest Telemetry Data',
                'telemetry'  => $telemetries
            ]);
    }

    public function calendarSchedules(Request $request, Garden $garden) : JsonResponse {
        $deviceSchedule = DeviceSchedule::query()
            ->whereHas('deviceSelenoid', fn($query) => $query->where('garden_id', $garden->id))
            ->active()
            ->firstOrFail();

        Validator::make([
            'month_year' => $request->query('month_year')
        ], [
            'month_year' => 'required|date_format:Y-m',
        ])
        ->validated();

        $now = now()->parse($request->query('month_year'));

        $startDateSchedule = $now->parse($deviceSchedule->start_date)->startOfMonth();
        $endDateSchedule = $now->parse($deviceSchedule->end_date)->endOfMonth();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        $periods = [];

        if (
            $startDate->gte($startDateSchedule) &&
            $startDate->lte($endDateSchedule) &&
            $endDate->lte($endDateSchedule)
        ) {
            $periods = $startDate->toPeriod($endDate, '1 days');
        } elseif (
            $startDate->lte($startDateSchedule) &&
            $endDate->gte($startDateSchedule) &&
            $endDate->lte($endDateSchedule)
        ) {
            $periods = $startDateSchedule->toPeriod($endDate, '1 days');
        }

        return response()->json([
            'message' => 'Calender schedule',
            'now' => collect($periods)->map(fn($period) => $period->format('Y-m-d'))->all()
        ]);
    }
}
