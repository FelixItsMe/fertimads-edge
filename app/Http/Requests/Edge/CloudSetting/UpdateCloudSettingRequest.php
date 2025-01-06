<?php

namespace App\Http\Requests\Edge\CloudSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCloudSettingRequest extends FormRequest
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
            'url' => 'required|string|url',
            'headers' => 'nullable|array',
            'headers.*' => 'nullable|array:key,value',
            'headers.*.key' => 'nullable|string|max:255|distinct:strict',
            'headers.*.value' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'headers.*.key' => 'Header key',
            'headers.*.value' => 'Header value',
        ];
    }
}
