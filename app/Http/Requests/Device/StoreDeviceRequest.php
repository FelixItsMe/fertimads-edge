<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
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
            'device_type_id'    => 'required|string|exists:device_types,id',
            'series'            => 'required|alpha_dash:ascii|max:255|unique:devices,series',
            'debit'             => 'sometimes|required|numeric|min:0',
            'latitude'          => 'sometimes|required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'         => 'sometimes|required|regex:/^(-?\d+(\.\d+)?)$/',
            'image'             => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'note'              => 'nullable|string|max:255',
        ];
    }
}
