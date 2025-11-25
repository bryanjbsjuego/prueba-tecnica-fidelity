<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                'max:150'
            ],
            'password' => [
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
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo válido.',
            'email.max' => 'El correo electrónico no debe exceder los 150 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una minúscula y un número.',
            'password.min' => 'La constraseña debe contener al menos 8 caracteres.',
            'password.max' => 'La contraseña no debe exceder los 35 caracteres'

        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'email' => trim(strtolower($this->email)),
            'password' => trim($this->password),
        ]);
    }


}
