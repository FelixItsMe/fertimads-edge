<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwsDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'series',
        'picture',
        'latitude',
        'longitude',
        'temperature',
        'humidity',
        'wind_speed',
        'rainfall',
        'max_temp',
        'min_temp',
    ];
}
