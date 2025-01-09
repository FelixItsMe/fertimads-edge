<?php

namespace App\Jobs;

use App\Enums\CloudExportLogEnums;
use App\Models\CloudExportLog;
use App\Models\CloudSetting;
use App\Models\FixStation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StoreFixStationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fixStationLastExported = FixStation::query()
            ->where('is_last_exported', 1)
            ->latest()
            ->first();

        $fixStationTelemetries = [];
        DB::table('fix_stations')
            ->select(['garden_id', 'samples', 'created_at'])
            ->when($fixStationLastExported?->created_at ?? null, function ($query) use ($fixStationLastExported) {
                $query->where('created_at', '>', $fixStationLastExported->created_at);
            })
            ->latest()
            ->chunk(100, function (Collection $fix_stations) use (&$fixStationTelemetries) {
                $fixStationTelemetries[] = $fix_stations->map(function ($fixStation) {
                    return [
                        'garden_id' => $fixStation->garden_id,
                        'created_at' => $fixStation->created_at,
                        'samples' => json_decode($fixStation->samples),
                    ];
                })->all();
            });

        try {
            if (count($fixStationTelemetries) > 0) {
                $cloudSetting = CloudSetting::first();

                $settingHeader = (array) $cloudSetting->headers;

                $headers = [
                    'Accept' => "application/json",
                    'Content-Type' => "application/json",
                    ...$settingHeader
                ];

                $response = Http::timeout(60)
                    ->withHeaders($headers)
                    ->post($cloudSetting->url, [
                        'data' => collect($fixStationTelemetries)->flatten(1)->toArray(),
                    ]);

                $response->throw();

                $fixStation = FixStation::query()
                    ->latest()
                    ->first();

                if ($fixStation) {
                    $fixStation->is_last_exported = 1;
                    $fixStation->save();
                }

                CloudExportLog::create([
                    'status' => CloudExportLogEnums::SUCCESS,
                    'message' => 'Data berhasil dikirim ke cloud!',
                ]);
            } else {
                CloudExportLog::create([
                    'status' => CloudExportLogEnums::FAILED,
                    'message' => 'Tidak ada data terbaru untuk dikirim!',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            CloudExportLog::create([
                'status' => CloudExportLogEnums::FAILED,
                'message' => $ex->getMessage(),
            ]);
        }
    }
}
