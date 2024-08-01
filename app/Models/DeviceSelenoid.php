<?php

namespace App\Models;

use App\Enums\GardenSelenoidModeEnums;
use App\Enums\GardenSelenoidStatusEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DeviceSelenoid extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'garden_id',
        'selenoid',
        'current_mode',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'current_mode' => GardenSelenoidModeEnums::class,
        'status' => GardenSelenoidStatusEnums::class,
    ];

    /**
     * Get the device that owns the DeviceSelenoid
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the garden that owns the DeviceSelenoid
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }

    /**
     * Get the deviceSensor associated with the DeviceSelenoid
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deviceSensor(): HasOne
    {
        return $this->hasOne(DeviceSensor::class);
    }

    /**
     * Get the activeDeviceSchedule associated with the DeviceSelenoid
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function activeDeviceSchedule(): HasOne
    {
        return $this->hasOne(DeviceSchedule::class)->where('is_finished', 0);
    }

    /**
     * Get all of the deviceSchedules for the DeviceSelenoid
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceSchedules(): HasMany
    {
        return $this->hasMany(DeviceSchedule::class);
    }

    /**
     * Get all of the deviceFertilizerSchedules for the DeviceSelenoid
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceFertilizerSchedules(): HasMany
    {
        return $this->hasMany(DeviceFertilizerSchedule::class);
    }
}
