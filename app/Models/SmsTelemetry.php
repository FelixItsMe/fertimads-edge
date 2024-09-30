<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsTelemetry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sms_garden_id',
        'latitude',
        'longitude',
        'samples',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'samples' => 'object',
    ];

    /**
     * Get the SmsGarden that owns the SmsTelemetry
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function smsGarden(): BelongsTo
    {
        return $this->belongsTo(SmsGarden::class);
    }
}
