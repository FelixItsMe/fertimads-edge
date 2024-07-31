<?php

namespace App\Http\Requests\Care;

use Illuminate\Foundation\Http\FormRequest;

class StorePestRequest extends FormRequest
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
            'file' => 'required|image',
            'garden_id' => 'required|exists:gardens,id',
            'commodity_id' => 'required|exists:commodities,id',
            'infected_count' => 'required',
            'gemini_prompt' => 'required'
        ];
    }
}
