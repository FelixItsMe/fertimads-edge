<?php

namespace App\Http\Requests\Management\PortableDevice;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortableDeviceRequest extends FormRequest
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
            'series'            => 'required|alpha_num:ascii|max:255|unique:portable_devices,series,' . $this->route('portable_device')->id,
            'version'           => 'required|string|max:255|regex:/^[0-9.]+$/',
            'image'             => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'production_date'   => 'required|date',
            'note'              => 'nullable|string|max:255',
        ];
    }
}
