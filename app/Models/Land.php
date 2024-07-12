<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Land extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'altitude',
        'polygon',
        'area',
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
     * Get all of the gardens for the Land
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gardens(): HasMany
    {
        return $this->hasMany(Garden::class);
    }
}
