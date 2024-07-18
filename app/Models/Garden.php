<?php

namespace App\Models;

use App\Enums\GardenSelenoidModeEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Garden extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'altitude',
        'polygon',
        'area',
        'color',
        'count_block',
        'commodity_id',
        'land_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'polygon' => 'array',
    ];

    /**
     * Get the commodity that owns the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    /**
     * Get the land that owns the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    /**
     * Get the deviceSelenoid associated with the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deviceSelenoid(): HasOne
    {
        return $this->hasOne(DeviceSelenoid::class);
    }

    public function pests(): HasMany
    {
        return $this->hasMany(Pest::class);
    }
}
