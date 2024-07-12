<?php

namespace App\Models;

use App\Enums\PhaseEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommodityPhase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'commodity_id',
        'phase',
        'age',
        'growth_phase',
        'kc',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'phase' => PhaseEnums::class,
    ];

    /**
     * Get the commodity that owns the CommodityPhase
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }
}
