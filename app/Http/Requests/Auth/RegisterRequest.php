<?php

namespace App\Http\Requests\Auth;

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
            'first_name' => [
                'required',
                'string',
                'max:128',
            ],
            'last_name' => [
                'required',
                'string',
                'max:128',
            ],
            'password' => [
                'required',
                Rules\Password::min(6)
                    ->max(64)
                    ->letters()
                    ->symbols()
                    ->numbers(),
            ],
            'national_code' => [
                'required',
                'string',
                'max:10',
                'national_code',
            ],
        ];
    }
}
