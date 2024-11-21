<?php

namespace App\Http\Requests\Management\WaterPipeline;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterPipelineRequest extends FormRequest
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
            'polyline' => json_decode($this->polyline),
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
            'description'   => 'nullable|string|max:255',
            'polyline'      => 'required|array',
            'polyline.*'    => 'required|array',
            'polyline.*.*'  => 'required|regex:/^(-?\d+(\.\d+)?)$/',
        ];
    }
}
