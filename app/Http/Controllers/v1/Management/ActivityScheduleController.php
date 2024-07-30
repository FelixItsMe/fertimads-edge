<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityScheduleController extends Controller
{
    public function index(): View
    {
        return view('pages.management.activity-schedule.index');
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
            ->active()
            ->get();

        $fertilizerSchedules = DeviceFertilizerSchedule::query()
            ->whereYear('execute_start', $year)
            ->whereMonth('execute_start', $month)
            ->when(request('garden_id'), function ($query) {
                $query->whereHas('deviceSelenoid', function ($query) {
                    $query->where('garden_id', request('garden_id'));
                });
            })
            ->active()
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

    public function detailScheduleDay($date): JsonResponse
    {
        $now = now()->parse($date);
        $types = collect();
        $waterTimes = collect();
        $fertilizerTimes = collect();

        $waterSchedules = DeviceSchedule::query()
            ->when(request('garden_id'), function ($query) {
                $query->whereRelation('deviceSelenoid', 'garden_id', request('garden_id'));
            }, function ($query) {
                $query->has('deviceSelenoid.garden');
            })
            ->where([
                ['start_date', '<=', $now->format('Y-m-d')],
                ['end_date', '>=', $now->format('Y-m-d')],
            ])
            ->active()
            ->get();

        if ($waterSchedules->count() > 0) {
            foreach ($waterSchedules as $waterSchedule) {
                $waterTimes->push($waterSchedule->execute_time);
            }

            $types->push('Penyiraman');
        }

        $fertilizerSchedules = DeviceFertilizerSchedule::query()
            ->when(request('garden_id'), function ($query) {
                $query->whereRelation('deviceSelenoid', 'garden_id', request('garden_id'));
            }, function ($query) {
                $query->has('deviceSelenoid.garden');
            })
            ->active()
            ->whereDate('execute_start', $now->copy()->format('Y-m-d'))
            ->get();

        if ($fertilizerSchedules->count() > 0) {
            foreach ($fertilizerSchedules as $fertilizerSchedule) {
                $fertilizerTimes->push(now()->parse($fertilizerSchedule->execute_start)->format('H:i:s'));
                $duration = now()
                    ->parse($fertilizerSchedule->execute_start)
                    ->diffInMinutes(
                        now()->parse($fertilizerSchedule->execute_end)
                    );
            }

            $types->push('Pemupukan');
        }

        return response()->json([
            'message' => 'Detail schedule',
            'types' => $types->all(),
            'waterTimes' => $waterTimes->all(),
            'fertilizeTimes' => $fertilizerTimes->all(),
        ]);
    }
}
