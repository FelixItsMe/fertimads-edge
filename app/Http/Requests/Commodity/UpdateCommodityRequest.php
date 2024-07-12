<?php

namespace App\Http\Requests\Commodity;

use App\Services\ImageService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommodityRequest extends FormRequest
{
    public function __construct(private ImageService $imageService)
    {
    }

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
            'description'   => 'required|string|max:2000',
        ];
    }
}
