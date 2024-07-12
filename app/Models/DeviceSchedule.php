<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_selenoid_id',
        'commodity_id',
        'type',
        'commodity_age',
        'start_date',
        'end_date',
        'execute_time',
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
     * Get the deviceSelenoid that owns the DeviceSensor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceSelenoid(): BelongsTo
    {
        return $this->belongsTo(DeviceSelenoid::class);
    }

    /**
     * Get the commodity that owns the DeviceSchedule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }
}
