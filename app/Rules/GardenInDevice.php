<?php

namespace App\Rules;

use App\Models\DeviceSelenoid;
use App\Models\Garden;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GardenInDevice implements ValidationRule
{
    public function __construct(public int $device_id) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !DeviceSelenoid::query()
                ->where('device_id', $this->device_id)
                ->where('garden_id', $value)
                ->first()
        ) {
            $fail('Kebun tidak terhubung dengan alat ini!');
        }
    }
}
