<?php

namespace App\Rules;

use App\Enums\DeviceTypeEnums;
use App\Models\DeviceSelenoid;
use App\Models\Garden;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;

class GardenDeviceAvailability implements ValidationRule
{
    public function __construct(public ?int $garden_id = null)
    {

    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $count = DeviceSelenoid::query()
            ->where('device_id', $value)
            ->whereHas('device.deviceType', function(Builder $query){
                $query->where('type', DeviceTypeEnums::HEAD_UNIT);
            })
            ->when($this->garden_id, function($query, $garden_id){
                $query->where('garden_id', $garden_id)
                    ->orWhereNull('garden_id');
            }, function ($query) {
                $query->whereNull('garden_id');
            })
            ->count();

        if ($count == 0) {
            $fail('Perangkat tidak ada / Perangkat tidak bisa dipakai karena sudah penuh!');
        }
    }
}
