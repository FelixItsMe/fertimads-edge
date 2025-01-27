<?php

namespace App\Jobs\RegionCodesImport;

use App\Models\RegionCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRegionCodeImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $rowData, private array $mapping)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $full_code = $this->rowData[$this->mapping['full_code']];
            $split_code = explode(".", $full_code);
            $region_name = $this->rowData[$this->mapping['region_name']];
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
            Log::info(json_encode($this->rowData));
        }
    }
}
