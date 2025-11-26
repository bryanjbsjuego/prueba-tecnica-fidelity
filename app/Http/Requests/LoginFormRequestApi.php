<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequestApi extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'usuario' => [
                'required',
                'string',
                'max:40'
            ],
            'contrasena' => [
                'required',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',
                'min:8',
                'max:35'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.string' => 'El usuario debe ser una cadena de texto',
            'usuario.max' => 'El usuario no debe exceder los 40 caracteres.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.regex' => 'La contraseña debe contener al menos una letra mayúscula, una minúscula y un número.',
            'contrasena.min' => 'La constraseña debe contener al menos 8 caracteres.',
            'contrasena.max' => 'La contraseña no debe exceder los 35 caracteres'

        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'usuario' => trim(strtolower($this->usuario)),
            'contrasena' => trim($this->contrasena),
        ]);
    }


}
