<?php

namespace App\Http\Requests\Edge\FixStation;

use Illuminate\Foundation\Http\FormRequest;

class StoreFixStationTelemetriesRequest extends FormRequest
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
            // 'url' => 'required|string|url',
            // 'headers' => 'nullable|array',
            // 'headers.*' => 'array:garden_id,samples,created_at|min:1',
            // 'data.*.garden_id' => 'required|string|max:255',
            // 'data.*.samples' => 'required|array:Nitrogen,Phosporus,Kalium,Ec,Ph,Temperature,Humidity',
        ];
    }
}
