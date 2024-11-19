<?php

namespace App\Jobs;

use App\Events\ImportFinishEvent;
use App\Jobs\RegionCodesImport\ProcessRegionCodeImportJob;
use App\Models\RegionCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 1200;  // 20-minute timeout
    public function __construct(private string $filePath, private int $userId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        RegionCode::query()->truncate();

        $mapping = [
            'full_code' => 0,
            'region_name' => 1
        ];
        $fileStream = fopen($this->filePath, 'r');
        $skipHeader = false;
        while ($row = fgetcsv($fileStream)) {
            if ($skipHeader) {
                $skipHeader = false;
                continue;
            }
            // dispatch(new ProcessRegionCodeImportJob($row, $mapping))
            //     ->onQueue('importProcess');
            try {
                $full_code = $row[$mapping['full_code']];
                $split_code = explode(".", $full_code);
                $region_name = $row[$mapping['region_name']];
                if ($region_name == null) {
                    preg_match('/"(.*?)"/', $split_code[count($split_code) - 1], $matches);

                    $region_name = $matches[1];

                    $full_code = explode(",", $full_code)[0];
                    $split_code = explode(".", $full_code);
                }

                RegionCode::insert(
                    [
                        'level_1' => $split_code[0],
                        'level_2' => (count($split_code) < 2 ? null : $split_code[1]),
                        'level_3' => (count($split_code) < 3 ? null : $split_code[2]),
                        'level_4' => (count($split_code) < 4 ? null : $split_code[3]),
                        'full_code' => $full_code,
                        'region_name' => $region_name,
                    ]
                );
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Log::info(json_encode($row));
            }
        }
        fclose($fileStream);
        unlink($this->filePath);  // Delete file after reading
        event(new ImportFinishEvent($this->userId));
        Cache::put('import', 2, 60 * 60 * 2);
    }
}
