<?php

namespace App\Http\Controllers\v1\Management;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\SmsGarden;
use App\Models\SmsTelemetry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SmsGardenController extends Controller
{
    public function show(SmsGarden $smsGarden) : View {
        $smsGarden->load('garden:id,name');
        $smsTelemetries = SmsTelemetry::query()
            ->where('sms_garden_id', $smsGarden->id)
            ->latest('created_at')
            ->paginate(10);

        return view('pages.garden.sms.show', compact('smsGarden', 'smsTelemetries'));
    }
}
