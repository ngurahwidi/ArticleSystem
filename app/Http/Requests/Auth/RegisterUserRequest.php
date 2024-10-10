<?php

namespace App\Http\Requests\Auth;

use GlobalXtreme\Validation\Support\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:11|unique:users',
            'profile' => 'nullable|file|mimes:jpeg,jpg,png|max:2048',
            'roleId' => 'required|integer',
            'bio' => 'nullable|string'
        ];
    }
}
