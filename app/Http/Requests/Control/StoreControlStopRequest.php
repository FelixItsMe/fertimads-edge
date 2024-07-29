<?php

namespace App\Http\Requests\Control;

use App\Rules\GardenHasSelenoid;
use Illuminate\Foundation\Http\FormRequest;

class StoreControlStopRequest extends FormRequest
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
            'type'      => 'required|string|in:penyiraman,pemupukanN,pemupukanP,pemupukanK',
            'garden_id' => ['required', new GardenHasSelenoid],
        ];
    }
}
