<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WetherWidget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['aws_device_id', 'open_api', 'region_code'];

    /**
     * Get the awsDevice that owns the WetherWidget
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function awsDevice(): BelongsTo
    {
        return $this->belongsTo(AwsDevice::class);
    }
}
