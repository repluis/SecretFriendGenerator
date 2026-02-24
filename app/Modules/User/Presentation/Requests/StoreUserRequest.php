<?php

namespace App\Modules\User\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre es obligatorio.',
            'name.string'    => 'El nombre debe ser un texto.',
            'name.max'       => 'El nombre no puede superar los 255 caracteres.',
            'email.string'   => 'El email debe ser un texto.',
            'email.email'    => 'El email debe ser una dirección válida.',
            'email.max'      => 'El email no puede superar los 255 caracteres.',
            'email.unique'   => 'El email ya está registrado.',
        ];
    }

    public function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json(['success' => false, 'errors' => $validator->errors()], 422)
        );
    }
}
