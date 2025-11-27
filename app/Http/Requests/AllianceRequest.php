<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class AllianceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid' => 'required|string',
            'categoria_cliente_id' => 'required|integer|exists:categorias_cliente,categoria_cliente_id',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'uuid.required' => 'La sesión UUID es obligatoria',
            'categoria_cliente_id.required' => 'La categoría del cliente es obligatoria',
            'categoria_cliente_id.exists' => 'Categoría de cliente no válida',
        ];
    }
}
