<?php

namespace App\Console\Commands;

use App\Jobs\StoreFixStationJob;
use App\Services\FixStationService;
use Illuminate\Console\Command;

class ExportCloudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-cloud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for scheduling export latest telemetry fix station to cloud!';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        StoreFixStationJob::dispatch()->onQueue('fix-station');
    }
}
