<?php

namespace App\Http\Controllers\v1\Management;

use App\Exports\SmsGardenTelemetryExport;
use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\SmsGarden;
use App\Models\SmsTelemetry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

    public function exportExcel(SmsGarden $smsGarden) {
        $smsGarden->load('garden:id,name');
        $smsTelemetries = SmsTelemetry::query()
            ->where('sms_garden_id', $smsGarden->id)
            ->latest('created_at')
            ->get();

        return Excel::download(new SmsGardenTelemetryExport($smsTelemetries), $smsGarden->created_at . " " . $smsGarden->garden->name . ".xlsx");
    }
}
