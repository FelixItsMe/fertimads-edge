<?php

namespace App\Http\Requests\Garden;

use App\Rules\GardenDeviceAvailability;
use Illuminate\Foundation\Http\FormRequest;

class StoreGardenRequest extends FormRequest
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
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $this->replace(['color' => substr($this->color, 1)]);
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
            'latitude'      => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'     => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'altitude'      => 'required|numeric',
            'polygon'       => 'required|array',
            'polygon.*'     => 'required|array',
            'polygon.*.*'   => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'commodity_id'  => 'nullable|exists:commodities,id',
            'land_id'       => 'required|exists:lands,id',
            'device_id'     => ['nullable', 'exists:devices,id', new GardenDeviceAvailability],
            'color'         => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'count_block'   => 'required|integer|min:0',
            'population'    => 'required|integer|min:0',
        ];
    }
}
