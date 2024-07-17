<?php

namespace App\Http\Requests\Control;

use App\Rules\GardenHasSelenoid;
use Illuminate\Foundation\Http\FormRequest;

class StoreControlScheduleWaterRequest extends FormRequest
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
            'garden_id'     => ['required', new GardenHasSelenoid],
            'start_date'    => 'required|date_format:Y-m-d',
            'commodity_age' => 'required|integer|min:0',
            'execute_time'  => 'required|date_format:H:i',
        ];
    }
}
