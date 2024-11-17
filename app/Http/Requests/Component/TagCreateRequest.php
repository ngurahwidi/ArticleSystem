<?php

namespace App\Http\Requests\Component;

use GlobalXtreme\Validation\Support\FormRequest;

class TagCreateRequest extends FormRequest
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
            'name' => 'required|string|unique:component_tags|max:100',
            'icon' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'statusId' => 'required|integer'
        ];
    }
}
