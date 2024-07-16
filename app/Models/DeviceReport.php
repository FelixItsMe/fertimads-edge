<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_selenoid_id',
        'mode',
        'type',
        'by_sensor',
        'total_time',
        'total_volume',
        'start_time',
        'end_time',
    ];

    /**
     * Get the deviceSelenoid that owns the DeviceReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceSelenoid(): BelongsTo
    {
        return $this->belongsTo(DeviceSelenoid::class);
    }
}
