<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_type_id',
        'series',
        'image',
        'note',
        'debit',
        'pumps',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'pumps' => 'object',
    ];

    /**
     * Get the deviceType that owns the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class);
    }

    /**
     * Get all of the deviceSelenoids for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceSelenoids(): HasMany
    {
        return $this->hasMany(DeviceSelenoid::class);
    }

    /**
     * Get all of the deviceSensors for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceSensors(): HasMany
    {
        return $this->hasMany(DeviceSensor::class);
    }

    /**
     * Get all of the activeDeviceSchedules for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function activeDeviceSchedules(): HasManyThrough
    {
        return $this->hasManyThrough(
            DeviceSchedule::class,
            DeviceSelenoid::class,
            'device_id',
            'device_selenoid_id',
        );
    }
}
