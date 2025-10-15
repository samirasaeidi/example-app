<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;
        return [
            'first_name' => [
                'required',
                'string',
                'max:128',
                Rule::unique('users', 'first_name')->ignore($this->user->id)
            ],
            'last_name' => [
                'required',
                'string',
                'max:128',
                Rule::unique('users', 'last_name')->ignore($this->user->id)
            ],
            'mobile' => [
                'required',
                'string',
                'max:11',
                'mobile',
                Rule::unique('users', 'mobile')->ignore($this->user->id)
            ],
            'password' => [
                'required',
                Rules\Password::min(6)
                    ->max(64)
                    ->letters()
                    ->symbols()
                    ->numbers(),
                Rule::unique('users', 'password')->ignore($this->user->id)
            ],
            'national_code' => [
                'required',
                'string',
                'max:10',
                'national_code',
                Rule::unique('users', 'national_code')->ignore($this->user->id)
            ],
        ];
    }
}
