<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Phase implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $prev_age = null;
        foreach ($value as $key => $phase) {
            if ($prev_age && $prev_age > $phase['age']) {
                $fail('Umur Fase ke-' . $key . ' lebih kecil dari fase sebelum!.');
            }

            $prev_age = $phase['age'];
        }
    }
}
