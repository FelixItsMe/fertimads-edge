<?php

namespace App\Http\Requests\User;

use App\Enums\UserRoleEnums;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role == 'su';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $this->route('user')->id,
            'role'      => 'required|string|in:' . collect(array_column(UserRoleEnums::cases(), 'value'))->join(','),
            'password'  => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
