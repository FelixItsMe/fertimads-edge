<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceScheduleExecute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_schedule_run_id',
        'start_time',
        'end_time',
        'total_volume',
    ];

    /**
     * Get the deviceScheduleRun that owns the DeviceScheduleExecute
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceScheduleRun(): BelongsTo
    {
        return $this->belongsTo(DeviceScheduleRun::class);
    }
}
