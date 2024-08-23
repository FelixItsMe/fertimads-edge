<?php

namespace App\Http\Requests\Care;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiseaseRequest extends FormRequest
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
            'image' => 'sometimes|file',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'symptoms' => 'required|string',
            'cause' => 'required|string',
            'control' => 'required|string',
            'pestisida' => 'required|string|max:255',
            'works_category' => 'required|string|max:255',
            'chemical' => 'required|string',
            'active_materials' => 'required|string',
        ];
    }
}
