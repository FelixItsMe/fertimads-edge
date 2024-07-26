<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceFertilizeScheduleExecute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_fertilizer_schedule_id',
        'type',
        'execute_start',
        'execute_end',
        'total_volume',
    ];

    /**
     * Get the deviceFertilizerSchedule that owns the DeviceFertilizeScheduleExecute
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceFertilizerSchedule(): BelongsTo
    {
        return $this->belongsTo(DeviceFertilizerSchedule::class);
    }
}
