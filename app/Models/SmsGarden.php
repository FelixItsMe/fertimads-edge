<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsGarden extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['portable_device_id', 'garden_id'];

    /**
     * Get the PortableDevice that owns the SmsGarden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function portableDevice(): BelongsTo
    {
        return $this->belongsTo(PortableDevice::class);
    }

    /**
     * Get the garden that owns the SmsGarden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }

    /**
     * Get all of the smsTelemetries for the SmsGarden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smsTelemetries(): HasMany
    {
        return $this->hasMany(SmsTelemetry::class);
    }
}
