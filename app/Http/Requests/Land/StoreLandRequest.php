<?php

namespace App\Http\Requests\Land;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'polygon' => json_decode($this->polygon),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'area'          => 'required|numeric|min:0',
            'address'       => 'required|string|max:2000',
            'latitude'      => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'     => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'altitude'      => 'required|numeric',
            'polygon'       => 'required|array',
            'polygon.*'     => 'required|array',
            'polygon.*.*'   => 'required|regex:/^(-?\d+(\.\d+)?)$/',
        ];
    }
}
