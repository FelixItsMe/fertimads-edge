<?php

namespace App\Http\Requests\Api\v1\DeviceControl;

use App\Rules\GardenInDevice;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceManualRequest extends FormRequest
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
            'garden_id' => ['required', new GardenInDevice($this->route('device')->id)],
            'status'    => 'required|integer|in:0,1',
        ];
    }
}
