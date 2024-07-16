<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceScheduleRun extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_schedule_id',
        'start_time',
        'end_time',
        'total_volume',
    ];

    /**
     * Get the deviceSchedule that owns the DeviceScheduleRun
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceSchedule(): BelongsTo
    {
        return $this->belongsTo(DeviceSchedule::class);
    }
}
