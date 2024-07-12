<?php

namespace App\Rules;

use App\Models\Commodity;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CommodityHavePhase implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exist = Commodity::query()
            ->has('commodityPhases')
            ->find($value);

        if (!$exist) {
            $fail('Komoditi tidak ditemukan/komoditi tidak memiliki data fase!');
        }
    }
}
