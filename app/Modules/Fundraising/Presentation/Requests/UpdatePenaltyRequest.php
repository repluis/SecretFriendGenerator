<?php

namespace App\Modules\Fundraising\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePenaltyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'penalty_amount' => 'required|numeric|min:0',
            'type'           => 'sometimes|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'penalty_amount.required' => 'El monto de la mora es obligatorio.',
            'penalty_amount.numeric'  => 'El monto de la mora debe ser un número.',
            'penalty_amount.min'      => 'El monto de la mora no puede ser negativo.',
            'type.string'             => 'El tipo debe ser un texto.',
            'type.max'                => 'El tipo no puede superar los 50 caracteres.',
        ];
    }

    public function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json(['success' => false, 'errors' => $validator->errors()], 422)
        );
    }
}
