<?php

namespace App\Rules;

use App\Models\Garden;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GardenHasSelenoid implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $garden = Garden::query()
            ->has('deviceSelenoid')
            ->find($value);

        if (!$garden) {
            $fail('Kebun tidak ditemukan!');
        }
    }
}
