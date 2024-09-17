<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceSchedule;
use App\Models\DeviceScheduleRun;
use App\Models\Garden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function gardenSchedulesInMonth(Garden $garden, int $year, int $month) : JsonResponse {
        $now = now()->parse("$year-$month-01");

        $startMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endMonth = $now->copy()->endOfMonth()->format('Y-m-d');

        $waterSchedules = DeviceSchedule::query()
            ->whereHas('deviceSelenoid', function(Builder $query)use($garden){
                $query->where('garden_id', $garden->id);
            })
            ->where(function(Builder $query)use($startMonth, $endMonth){
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
            ->get();

        $fertilizerSchedules = DeviceFertilizerSchedule::query()
            ->where('garden_id', $garden->id)
            ->whereYear('execute_start', $year)
            ->whereMonth('execute_start', $month)
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
                    $schedule->push('Penyiraman');
                    break;
                }
            }

            foreach ($fertilizerSchedules as $fertilizerSchedule) {
                $executeDate = now()->parse($fertilizerSchedule->execute_start)->startOfDay();
                if (
                    $datetime->startOfDay()->equalTo($executeDate)
                ) {
                    $schedule->push('Pemupukan');
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
            'schedules' => $schedules,
        ]);
    }

    public function detailScheduleDay(Garden $garden, $date) : JsonResponse {
        $now = now()->parse($date);
        $types = collect();
        $waterTimes = collect();
        $fertilizerTimes = collect();

        $waterSchedule = DeviceScheduleRun::query()
            ->with([
                'deviceScheduleExecute',
                'deviceSchedule:id,commodity_age,start_date,is_finished',
            ])
            ->whereHas('deviceSchedule', function(Builder $query)use($garden){
                $query->where('garden_id', $garden->id);
            })
            ->where(function($query){
                $query->whereHas('deviceSchedule', function(Builder $query){
                    $query->where('is_finished', 0);
                })
                ->orWhere(function(Builder $query){
                    $query->whereHas('deviceSchedule', function(Builder $query){
                        $query->where('is_finished', 1);
                    })
                    ->has('deviceScheduleExecute');
                });
            })
            ->whereDate('start_time', $now->format('Y-m-d'))
            ->get();

        $fertilizerSchedules = DeviceFertilizerSchedule::query()
            ->with([
                'scheduleExecute' => function($query){
                    $query->whereNotNull('execute_end');
                }
            ])
            ->where('garden_id', $garden->id)
            ->whereDate('execute_start', $now->copy()->format('Y-m-d'))
            ->get();

        return response()->json([
            'message' => 'Detail schedule',
            'waterSchedule' => $waterSchedule,
            'fertilizerSchedules' => $fertilizerSchedules,
        ]);
    }
}
