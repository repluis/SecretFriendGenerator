<?php

namespace App\Modules\User\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfilePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string|max:255',
            'new_password'     => 'required|string|min:6|max:255|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'  => 'La contraseña actual es obligatoria.',
            'current_password.string'    => 'La contraseña actual debe ser un texto.',
            'current_password.max'       => 'La contraseña actual no puede superar los 255 caracteres.',
            'new_password.required'      => 'La nueva contraseña es obligatoria.',
            'new_password.string'        => 'La nueva contraseña debe ser un texto.',
            'new_password.min'           => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'new_password.max'           => 'La nueva contraseña no puede superar los 255 caracteres.',
            'new_password.confirmed'     => 'La confirmación de contraseña no coincide.',
        ];
    }

    public function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            back()->withErrors($validator)->withInput()
        );
    }
}
