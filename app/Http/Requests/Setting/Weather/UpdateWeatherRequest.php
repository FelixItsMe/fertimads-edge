<?php

namespace App\Http\Requests\Setting\Weather;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWeatherRequest extends FormRequest
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
            'aws_device_id' => 'nullable|exists:aws_devices,id'
        ];
    }
}
