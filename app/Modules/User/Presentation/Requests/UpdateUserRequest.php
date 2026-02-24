<?php

namespace App\Modules\User\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string'   => 'El nombre debe ser un texto.',
            'name.max'      => 'El nombre no puede superar los 255 caracteres.',
        ];
    }

    public function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json(['success' => false, 'errors' => $validator->errors()], 422)
        );
    }
}
