<?php

namespace App\Models;

use App\Enums\FertilizerScheduleTypeEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DeviceFertilizerSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'garden_id',
        'device_selenoid_id',
        'is_finished',
        'type',
        'execute_start',
        'execute_end',
        'total_volume',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => FertilizerScheduleTypeEnums::class,
    ];

    /**
     * Scope a query to only include active schedule
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_finished', 0);
    }

    /**
     * Scope a query to only include finished schedule
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFinished($query)
    {
        return $query->where('is_finished', 1);
    }

    /**
     * Get the garden that owns the DeviceFertilizerSchedule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }

    /**
     * Get the deviceSelenoid that owns the DeviceFertilizerSchedule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceSelenoid(): BelongsTo
    {
        return $this->belongsTo(DeviceSelenoid::class);
    }

    /**
     * Get the scheduleExecute associated with the DeviceFertilizerSchedule
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function scheduleExecute(): HasOne
    {
        return $this->hasOne(DeviceFertilizeScheduleExecute::class);
    }
}
