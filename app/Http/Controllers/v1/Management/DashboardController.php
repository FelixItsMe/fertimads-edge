<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\DeviceFertilizerSchedule;
use App\Models\DeviceScheduleRun;
use App\Models\Garden;
use App\Models\Land;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index() : View {
        $scheduleCategory = request()->query('category', null);

        $fertilizeSchedules = [];

        if (!$scheduleCategory || $scheduleCategory == 'fertilizer') {
            $fertilizeSchedules = DeviceFertilizerSchedule::query()
                ->with('deviceSelenoid.garden:id,name')
                ->has('deviceSelenoid.garden')
                ->finished()
                ->get();
        }

        $waterSchedules = [];

        if (!$scheduleCategory || $scheduleCategory == 'water') {
            $waterSchedules = DeviceScheduleRun::query()
                ->with('deviceSchedule.deviceSelenoid.garden:id,name')
                ->has('deviceSchedule.deviceSelenoid.garden')
                ->get();
        }

        $gardens = Garden::query()
            ->select(['id', 'name', 'area', 'commodity_id'])
            ->with([
                'commodity:id,name'
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $activityLog = Activity::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $sumArea = Land::query()
            ->sum('area');

        $countGarden = Garden::count();
        $countCommodity = Commodity::count();
        $countTool = Tool::count();
        $countUser = User::count();

        return view('pages.management.dashboard.index', compact(
            'fertilizeSchedules',
            'waterSchedules',
            'activityLog',
            'gardens',
            'sumArea',
            'countGarden',
            'countCommodity',
            'countTool',
            'countUser',
        ));
    }
}
