<?php

namespace App\Modules\Auth\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identification' => 'required|string|max:100',
            'password'       => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'identification.required' => 'La identificación es obligatoria.',
            'identification.string'   => 'La identificación debe ser un texto.',
            'identification.max'      => 'La identificación no puede superar los 100 caracteres.',
            'password.required'       => 'La contraseña es obligatoria.',
            'password.string'         => 'La contraseña debe ser un texto.',
            'password.max'            => 'La contraseña no puede superar los 255 caracteres.',
        ];
    }

    public function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            back()
                ->withInput($this->only('identification', 'remember'))
                ->withErrors($validator)
        );
    }
}
