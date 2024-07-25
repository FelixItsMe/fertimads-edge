<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $appends = ['gemini_response_decoded'];

    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function geminiResponseDecoded(): Attribute
    {
        return Attribute::get(function ($values, $attributes) {
            return json_decode($this->gemini_response);
        });
    }
}
