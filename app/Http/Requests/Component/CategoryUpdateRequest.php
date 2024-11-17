<?php

namespace App\Http\Requests\Component;

use GlobalXtreme\Validation\Support\FormRequest;

class CategoryUpdateRequest extends FormRequest
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
            'name' => 'string|unique:component_categories|max:100',
            'icon' => 'file|mimes:jpg,jpeg,png|max:2048|nullable',
            'statusId' => 'integer'
        ];
    }
}
