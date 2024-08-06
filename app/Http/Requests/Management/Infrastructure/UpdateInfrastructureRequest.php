<?php

namespace App\Http\Requests\Management\Infrastructure;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfrastructureRequest extends FormRequest
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
            'image'         => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'name'          => 'required|string|max:255',
            'quantity'      => 'required|integer|min:0',
            'description'   => 'required|string|max:2000',
        ];
    }
}
