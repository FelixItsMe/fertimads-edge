<?php

namespace App\Http\Controllers\v1\Management;

use App\Exports\SmsGardenTelemetryExport;
use App\Http\Controllers\Controller;
use App\Models\SmsGarden;
use App\Models\SmsTelemetry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SmsGardenController extends Controller
{
    public function show(SmsGarden $smsGarden) : View {
        $smsGarden->load('garden:id,name');
        $smsTelemetries = SmsTelemetry::query()
            ->where('sms_garden_id', $smsGarden->id)
            ->latest('created_at')
            ->paginate(10);

        $avg = SmsTelemetry::query()
            ->select(DB::raw(
                'AVG(JSON_EXTRACT(samples, "$.ph")) as ph,' .
                'AVG(JSON_EXTRACT(samples, "$.ec")) as ec,' .
                'AVG(JSON_EXTRACT(samples, "$.soil_moisture")) as soil_moisture,' .
                'AVG(JSON_EXTRACT(samples, "$.ambient_humidity")) as ambient_humidity,' .
                'AVG(JSON_EXTRACT(samples, "$.soil_temperature")) as soil_temperature,' .
                'AVG(JSON_EXTRACT(samples, "$.ambient_temperature")) as ambient_temperature,' .
                'AVG(JSON_EXTRACT(samples, "$.n")) as n,' .
                'AVG(JSON_EXTRACT(samples, "$.p")) as p,' .
                'AVG(JSON_EXTRACT(samples, "$.k")) as k'
            ))
            ->first()
            ->toArray();

        return view('pages.garden.sms.show', compact('smsGarden', 'smsTelemetries', 'avg'));
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
