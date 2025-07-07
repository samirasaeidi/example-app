<?php

namespace App\Http\Requests;

use App\Rules\MobileRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;


class RegisterRequest extends FormRequest
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
            'mobile' => [
                'required',
                'string',
                'max:11',
                'mobile',
            ],
            'parents' => [
                'required',
                'array',
            ],
            'parents.*' => [
                'required',
                'array',
            ],
            'parents.*.mobile' => [
                'mobile',
                'required',
            ],
            'parents.*.type' => [
                'required',
                'string',
                new Rules\In(['father', 'mother']),
            ],
        ];
    }
}
