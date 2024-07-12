<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSensor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_selenoid_id',
        'sensors',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sensors' => 'json',
    ];

    /**
     * Get the deviceSelenoid that owns the DeviceSensor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceSelenoid(): BelongsTo
    {
        return $this->belongsTo(DeviceSelenoid::class);
    }
}
