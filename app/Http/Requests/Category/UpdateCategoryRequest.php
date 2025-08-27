<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category')->id;

        return [
            'name' => [
                'required',
                'max:255',
                'string',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'required',
                'max:255',
                'string',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'parent_id' => [
                'sometimes',
                'nullable',
                'int',
                'exists:categories,id',
            ],
            'active' => [
                'sometime',
                'nullable',
                'int',
            ],

        ];
    }

    public function prepareForValidation()
    {
        if ($this->name) {
            $this->merge([
                'slug' => createSlug($this->name),
            ]);
        }

    }
}
