<?php

namespace App\Models;

use App\Enums\MapObjectType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MapObject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $appends = ['custom_icon'];

    public function objectable(): MorphTo
    {
        return $this->morphTo('objectable');
    }

    public function type(): Attribute
    {
        return Attribute::get(function ($value) {
            return MapObjectType::getLabelTexts()[$value];
        });
    }

    public function customIcon(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            $icon = null;

            switch ($attributes['type']) {
                case MapObjectType::GEDUNG_PUSAT->value:
                    $icon = asset('assets/leaflet/Pin-Building.svg');
                    break;
                case MapObjectType::EMBUNG->value:
                    $icon = asset('assets/leaflet/Pin-Embung.svg');
                    break;
                case MapObjectType::SAUNG_HEADUNIT->value:
                    $icon = asset('assets/leaflet/Pin-House-Flag.svg');
                    break;
                case MapObjectType::TOREN->value:
                    $icon = asset('assets/leaflet/Pin-Water-Tank.svg');
                    break;
                case MapObjectType::SMS_FIX_STATION->value:
                    $icon = asset('assets/leaflet/Pin-SMS-Fix-Station.svg');
                    break;
                case MapObjectType::PENANGKAL_PETIR->value:
                    $icon = asset('assets/leaflet/Pin-Lightning-Rod.svg');
                    break;
                default:
                    $icon = null;
                    break;
            }

            return $icon;
        });
    }
}
