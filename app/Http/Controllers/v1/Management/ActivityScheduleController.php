<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceSchedule;
use App\Models\DeviceScheduleRun;
use App\Models\Garden;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityScheduleController extends Controller
{
    public function index(): View
    {
        $gardens = Garden::query()
            ->select(['id', 'name'])
            ->with('deviceSelenoid:id,garden_id,selenoid')
            ->has('deviceSelenoid')
            ->get();

        return view('pages.management.activity-schedule.index', compact('gardens'));
    }

    public function scheduleInMonth(int $year, int $month): JsonResponse
    {
        $now = now()->parse("$year-$month-01");

        $startMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endMonth = $now->copy()->endOfMonth()->format('Y-m-d');

        $waterSchedules = DeviceSchedule::query()
            ->where(function (Builder $query) use ($startMonth, $endMonth) {
                $query->where([
                    ['start_date', '>=', $startMonth],
                    ['end_date', '>=', $endMonth],
                ])
                    ->orWhere([
                        ['start_date', '<=', $startMonth],
                        ['end_date', '>=', $startMonth],
                        ['end_date', '<=', $endMonth],
                    ])
                    ->orWhere([
                        ['start_date', '<=', $startMonth],
                        ['end_date', '>=', $endMonth],
                    ]);
            })
            ->when(request('garden_id'), function ($query) {
                $query->whereHas('deviceSelenoid', function ($query) {
                    $query->where('garden_id', request('garden_id'));
                });
            })
            ->get();

        $fertilizerSchedules = DeviceFertilizerSchedule::query()
            ->whereYear('execute_start', $year)
            ->whereMonth('execute_start', $month)
            ->when(request('garden_id'), function ($query) {
                $query->whereHas('deviceSelenoid', function ($query) {
                    $query->where('garden_id', request('garden_id'));
                });
            })
            ->get();

        $period = now()->parse($startMonth)->toPeriod($endMonth, 1, 'days');

        $schedules = collect();
        foreach ($period as $datetime) {
            $schedule = collect();
            foreach ($waterSchedules as $waterSchedule) {
                $startDate = now()->parse($waterSchedule->start_date);
                $endDate = now()->parse($waterSchedule->end_date);
                if (
                    $datetime->gte($startDate) &&
                    $datetime->lte($endDate)
                ) {
                    $schedule->push(1);
                    break;
                }
            }

            foreach ($fertilizerSchedules as $fertilizerSchedule) {
                $executeDate = now()->parse($fertilizerSchedule->execute_start)->startOfDay();
                if (
                    $datetime->startOfDay()->equalTo($executeDate)
                ) {
                    $schedule->push(2);
                    break;
                }
            }

            $schedules->push([
                'date' => $datetime->format('Y-m-d'),
                'schedule' => $schedule->all(),
            ]);
        }

        return response()->json([
            'message' => 'Schedule',
            'time' => [
                $startMonth, $endMonth
            ],
            'water' => $waterSchedules,
            'fertilizer' => $fertilizerSchedules,
            'schedules' => $schedules,
        ]);
    }

    public function gardensScheduleDay($date): JsonResponse
    {
        $now = now()->parse($date);

        $gardens = Garden::query()
            ->select(['id', 'name'])
            ->with('deviceSelenoid:id,garden_id,selenoid')
            ->whereHas('deviceSchedules.deviceScheduleRuns', function($query)use($now){
                $query->whereDate('start_time', $now->format('Y-m-d'));
            })
            ->orWhereHas('deviceFertilizerSchedules', function($query)use($now){
                $query->whereDate('execute_start', $now->format('Y-m-d'));
            })
            ->get();

        return response()->json([
            'message' => 'Detail schedule',
            'gardens' => $gardens,
        ]);
    }

    public function detailGardenScheduleDay($date, Garden $garden) : JsonResponse {
        $now = now()->parse($date)->format('Y-m-d');
        $waterSchedules = DeviceScheduleRun::query()
            ->with([
                'deviceScheduleExecute' => function($query){
                    $query->whereNotNull('end_time');
                }
            ])
            ->whereHas('deviceSchedule', function(Builder $query)use($garden){
                $query->where('garden_id', $garden->id);
            })
            ->whereDate('start_time', $now)
            ->get();

        $fertilizerSchedules = DeviceFertilizerSchedule::query()
            ->with([
                'scheduleExecute' => function($query){
                    $query->whereNotNull('execute_end');
                }
            ])
            ->whereDate('execute_start', $now)
            ->where('garden_id', $garden->id)
            ->get();

        return response()->json([
            'message' => 'Detail jadwal pada kebun',
            'waterSchedules' => $waterSchedules,
            'fertilizerSchedules' => $fertilizerSchedules,
        ]);
    }
}
