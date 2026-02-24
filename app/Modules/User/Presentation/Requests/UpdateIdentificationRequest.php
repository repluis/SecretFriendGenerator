<?php

namespace App\Modules\User\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateIdentificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identification' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'identification.required' => 'La identificación es obligatoria.',
            'identification.string'   => 'La identificación debe ser un texto.',
            'identification.max'      => 'La identificación no puede superar los 100 caracteres.',
        ];
    }

    public function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json(['success' => false, 'errors' => $validator->errors()], 422)
        );
    }
}
