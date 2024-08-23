<?php

namespace App\Http\Controllers\v1\Care;

use App\Http\Controllers\Controller;
use App\Models\DeviceReport;
use App\Models\Disease;
use App\Models\Pest;
use App\Models\Weeds;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $diseaseCount = Disease::count();
        $weedsCount = Weeds::count();
        $pestCount = Pest::count();
        $fertilizerCount = DeviceReport::query()
            ->where('type', 'like', '%pemupukan%')
            ->count();

        return view('pages.care.dashboard.index', compact('diseaseCount', 'weedsCount', 'pestCount', 'fertilizerCount'));
    }
}
