<?php

namespace App\Http\Requests\CommodityPhase;

use App\Enums\PhaseEnums;
use App\Rules\Phase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePhaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phase'               => ['required', 'array', 'size:5', 'required_array_keys:' . collect(array_column(PhaseEnums::cases(), 'value'))->join(','), new Phase],
            'phase.*'               => ['required'],
            'phase.*.age'           => 'required|numeric|min:0',
            'phase.*.growth_phase'  => 'required|numeric|min:0',
            'phase.*.kc'            => 'required|numeric|min:0',
        ];
    }
}
