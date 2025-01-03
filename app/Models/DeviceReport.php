<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceReport extends Model
{
    use HasFactory;

    public $appends = ['pemupukan_type'];

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

    public function pemupukanType(): Attribute
    {
        return Attribute::get(function () {
            $type = str_replace('pemupukan', '', $this->type);

            switch ($type) {
                case 'N':
                    return 'Nitrogen';
                case 'P':
                    return 'Fosfor';
                case 'K':
                    return 'Kalium';
                default:
                    return '-';
            }
        });
    }

    public function timeInHours(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            return number_format($attributes['total_time'] / 3600, 2);
        });
    }
}
