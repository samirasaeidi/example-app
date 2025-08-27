<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCategory extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                'string',
                'unique:categories,name',
            ],
            'slug' => [
                'required',
                'max:255',
                'string',
                'unique:categories,slug',
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
