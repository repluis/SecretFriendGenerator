<?php

namespace App\Modules\Transaction\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'     => 'required|integer|exists:users,id',
            'type'        => 'required|string|in:credit,debit',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'    => 'El usuario es obligatorio.',
            'user_id.integer'     => 'El usuario debe ser un número entero.',
            'user_id.exists'      => 'El usuario seleccionado no existe.',
            'type.required'       => 'El tipo de transacción es obligatorio.',
            'type.string'         => 'El tipo de transacción debe ser un texto.',
            'type.in'             => 'El tipo debe ser "credit" o "debit".',
            'amount.required'     => 'El monto es obligatorio.',
            'amount.numeric'      => 'El monto debe ser un número.',
            'amount.min'          => 'El monto debe ser mayor a 0.',
            'description.string'  => 'La descripción debe ser un texto.',
            'description.max'     => 'La descripción no puede superar los 255 caracteres.',
        ];
    }

    public function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json(['success' => false, 'errors' => $validator->errors()], 422)
        );
    }
}
