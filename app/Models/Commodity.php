<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Commodity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'description',
    ];

    /**
     * Get all of the gardens for the Commodity
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gardens(): HasMany
    {
        return $this->hasMany(Garden::class);
    }

    /**
     * Get all of the commodity_phases for the Commodity
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commodityPhases(): HasMany
    {
        return $this->hasMany(CommodityPhase::class)->orderBy('phase');
    }

    /**
     * Get the lastCommodityPhase associated with the Commodity
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastCommodityPhase(): HasOne
    {
        return $this->hasOne(CommodityPhase::class)->ofMany('phase', 'max');
    }

    public function pests(): HasMany
    {
        return $this->hasMany(Pest::class);
    }
}
