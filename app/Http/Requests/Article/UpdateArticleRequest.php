<?php

namespace App\Http\Requests\Article;

use GlobalXtreme\Validation\Support\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'title' => 'string|max:100',
            'description' => 'string|max:170',
            'content' => 'string',
            'featuredImage' => 'file|mimes:jpg,jpeg,png|max:2048|nullable',
            'galleries' => 'array',
            'statusId' => 'integer',
            'categoryIds' => 'array',
            'tagIds' => 'array'
        ];
    }
}
