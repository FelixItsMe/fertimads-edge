<?php

namespace App\Http\Controllers\v1\Care;

use App\Http\Controllers\Controller;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityScheduleController extends Controller
{
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
        $water = collect();
        $fertilize = collect();

        $waterSchedules = DeviceSchedule::query()
            ->when(request('garden_id'), function ($query) {
                $query->whereRelation('deviceSelenoid', 'garden_id', request('garden_id'));
            }, function ($query) {
                $query->has('deviceSelenoid.garden');
            })
            ->with(['deviceScheduleRuns' => function ($query) use ($now) {
                $query
                    ->whereDate('start_time', '<=', $now->format('Y-m-d'))
                    ->whereDate('end_time', '>=', $now->format('Y-m-d'))
                    ->with('deviceScheduleExecute');
            }])
            ->where([
                ['start_date', '<=', $now->format('Y-m-d')],
                ['end_date', '>=', $now->format('Y-m-d')],
            ])
            ->active()
            ->get();

        if ($waterSchedules->count() > 0) {
            foreach ($waterSchedules as $waterSchedule) {
                if (count($waterSchedule?->deviceScheduleRuns) > 0) {
                    $duration = now()
                        ->parse($waterSchedule->deviceScheduleRuns[0]->start_time)
                        ->diffInMinutes(
                            now()->parse($waterSchedule->deviceScheduleRuns[0]->end_time)
                        );

                    $actualDuration = now()
                        ->parse($waterSchedule->deviceScheduleRuns[0]->deviceScheduleExecute?->start_time)
                        ->diffInMinutes(
                            now()->parse($waterSchedule->deviceScheduleRuns[0]->deviceScheduleExecute?->end_time)
                        );
                }

                $water->add([
                    'waktu_mulai' => $waterSchedule->execute_time,
                    'total_volume' => $waterSchedule?->deviceScheduleRuns->first()?->deviceScheduleExecute?->total_volume,
                    'estimasi_volume' => $waterSchedule?->deviceScheduleRuns->first()?->total_volume,
                    'total_waktu' => $actualDuration ?? 0,
                    'estimasi_waktu' => $duration ?? 0,
                ]);
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
                $duration = now()
                    ->parse($fertilizerSchedule->execute_start)
                    ->diffInMinutes(
                        now()->parse($fertilizerSchedule->execute_end)
                    );

                $actualDuration = now()
                    ->parse($fertilizerSchedule->scheduleExecute?->execute_start)
                    ->diffInMinutes(
                        now()->parse($fertilizerSchedule->scheduleExecute?->execute_end)
                    );
            }

            $fertilize->add([
                'waktu_mulai' => now()->parse($fertilizerSchedule->execute_start)->format('H:i:s'),
                'total_volume' => $fertilizerSchedule?->scheduleExecute?->total_volume,
                'estimasi_volume' => $fertilizerSchedule?->total_volume,
                'total_waktu' => $actualDuration ?? 0,
                'estimasi_waktu' => $duration ?? 0,
            ]);

            $types->push('Pemupukan');
        }

        return response()->json([
            'message' => 'Detail schedule',
            'types' => $types->all(),
            'water' => $water->values(),
            'fertilize' => $fertilize->values(),
        ]);
    }
}
