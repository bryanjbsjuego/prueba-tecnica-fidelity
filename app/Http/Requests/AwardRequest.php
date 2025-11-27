<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AwardRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    /**
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'session' => 'required|string',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'sort_by' => 'nullable|in:name,points,money',
            'order' => 'nullable|in:asc,desc',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'session.required' => 'La sesión es requerida',
            'session.string' => 'La sesión debe ser una cadena de texto',
            'page.integer' => 'La página debe ser un número entero',
            'page.min' => 'La página debe ser al menos 1',
            'limit.integer' => 'El límite debe ser un número entero',
            'limit.min' => 'El límite debe ser al menos 1',
            'limit.max' => 'El límite no puede ser mayor a 100',
            'search.string' => 'La búsqueda debe ser texto',
            'search.max' => 'La búsqueda no puede exceder 255 caracteres',
            'sort_by.in' => 'El campo de ordenamiento debe ser: name, points o money',
            'order.in' => 'El orden debe ser: asc o desc',
        ];
    }

    /**
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'session' => 'sesión',
            'page' => 'página',
            'limit' => 'límite',
            'search' => 'búsqueda',
            'sort_by' => 'ordenar por',
            'order' => 'orden',
        ];
    }
}