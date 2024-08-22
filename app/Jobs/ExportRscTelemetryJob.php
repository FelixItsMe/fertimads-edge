<?php

namespace App\Jobs;

use App\Events\ExportCompletedEvent;
use App\Exports\DeviceTelemetryExport;
use App\Models\DeviceTelemetry;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportRscTelemetryJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $from, protected string $to, protected User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $queryFrom = $this->from;
        $queryTo = $this->to;

        $formatData = [];

        foreach (
            DeviceTelemetry::query()
                ->when(($queryFrom == $queryTo), function (Builder $query, $a) use ($queryFrom) {
                    $query->whereDate('created_at', $queryFrom);
                })
                ->when(($queryFrom != $queryTo), function (Builder $query, $a) use ($queryFrom, $queryTo) {
                    $query->where('created_at', '>=', $queryFrom)
                        ->where('created_at', '<=', $queryTo);
                })
                ->lazy() as $deviceTelemetry
        ) {
            $telemetry = (array) $deviceTelemetry->telemetry;
            for ($i = 1; $i <= 4; $i++) {
                $formatData[] = (object) [
                    'created_at' => $deviceTelemetry->created_at,
                    'selenoid' => $i,
                    'N' => number_format($telemetry['SS' . $i]->N, 2) . ' mg/kg',
                    'P' => number_format($telemetry['SS' . $i]->P, 2) . ' mg/kg',
                    'K' => number_format($telemetry['SS' . $i]->K, 2) . ' mg/kg',
                    'EC' => number_format($telemetry['SS' . $i]->EC, 2) . ' uS/cm',
                    'pH' => number_format($telemetry['SS' . $i]->pH, 2),
                    'T' => number_format($telemetry['SS' . $i]->T, 2) . "°C",
                    'H' => number_format($telemetry['SS' . $i]->H, 2) . "%",
                    'dhtT' => number_format($telemetry['DHT1']->T, 2) . "°C",
                    'dhtH' => number_format($telemetry['DHT1']->H, 2) . "%",
                ];
            }
        }

        $path = 'export/excel/';
        $fileName = 'rsc-telemetri-'. $this->user->id .'.xlsx';

        Excel::store(new DeviceTelemetryExport(collect($formatData)), $path . $fileName);
        // Storage::putFile($path . $fileName, (new FastExcel(User::all()))->download('file.xlsx'));

        event(new ExportCompletedEvent($this->user->id));
    }
}
