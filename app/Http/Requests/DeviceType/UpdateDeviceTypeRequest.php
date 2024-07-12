<?php

namespace App\Http\Requests\DeviceType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceTypeRequest extends FormRequest
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
            'name'          => 'required|string|max:255|unique:device_types,name,' . $this->route('device_type')->id,
            'version'       => 'required|string|max:255|regex:/^[0-9.]+$/',
            'image'         => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'description'   => 'required|string|max:5000',
        ];
    }
}
