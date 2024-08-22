<?php

namespace App\Http\Requests\Management\Aws;

use Illuminate\Foundation\Http\FormRequest;

class StoreAwsDeviceRequest extends FormRequest
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
            'series'    => 'required|alpha_num:ascii|max:255|unique:aws_devices,series',
            'image'     => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'latitude'  => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
        ];
    }
}
