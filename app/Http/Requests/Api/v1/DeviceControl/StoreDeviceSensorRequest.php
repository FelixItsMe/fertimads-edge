<?php

namespace App\Http\Requests\Api\v1\DeviceControl;

use App\Rules\GardenInDevice;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceSensorRequest extends FormRequest
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
            'garden_id'                 => ['required', new GardenInDevice($this->route('device')->id)],
            'humidity'                  => 'nullable|array:enable,upper_limit,lower_limit',
            'humidity.enable'           => 'boolean',
            'humidity.upper_limit'      => 'required_if_accepted:humidity.enable|nullable|numeric',
            'humidity.lower_limit'      => 'required_if_accepted:humidity.enable|nullable|numeric|lte:humidity.upper_limit',
            'nitrogen'                  => 'nullable|array:enable,upper_limit,lower_limit',
            'nitrogen.enable'           => 'required|boolean',
            'nitrogen.upper_limit'      => 'required_if_accepted:nitrogen.enable|nullable|numeric',
            'nitrogen.lower_limit'      => 'required_if_accepted:nitrogen.enable|nullable|numeric|lte:nitrogen.upper_limit',
            'phosphorus'                => 'nullable|array:enable,upper_limit,lower_limit',
            'phosphorus.enable'         => 'required|boolean',
            'phosphorus.upper_limit'    => 'required_if_accepted:phosphorus.enable|nullable|numeric',
            'phosphorus.lower_limit'    => 'required_if_accepted:phosphorus.enable|nullable|numeric|lte:phosphorus.upper_limit',
            'kalium'                    => 'nullable|array:enable,upper_limit,lower_limit',
            'kalium.enable'             => 'required|boolean',
            'kalium.upper_limit'        => 'required_if_accepted:kalium.enable|nullable|numeric',
            'kalium.lower_limit'        => 'required_if_accepted:kalium.enable|nullable|numeric|lte:kalium.upper_limit',
        ];
    }
}
