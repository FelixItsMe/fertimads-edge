<?php

namespace App\Jobs;

use App\Events\ExportCompletedEvent;
use App\Events\ExportConpletedEvent;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class NotifyUserOfCompletedExport implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        $this->onQueue('NotifyCompletedExport');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        event(new ExportCompletedEvent($this->user->id));
    }
}
