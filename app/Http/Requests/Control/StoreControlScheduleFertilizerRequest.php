<?php

namespace App\Http\Requests\Control;

use App\Enums\FertilizerScheduleTypeEnums;
use App\Rules\GardenHasSelenoid;
use Illuminate\Foundation\Http\FormRequest;

class StoreControlScheduleFertilizerRequest extends FormRequest
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
            'execute_date'  => 'required|date_format:Y-m-d',
            'execute_time'  => 'required|date_format:H:i',
            'volume'        => 'required|numeric|min:0',
            'type'          => 'required|string|in:' . collect(array_column(FertilizerScheduleTypeEnums::cases(), 'value'))->join(','),
        ];
    }
}
