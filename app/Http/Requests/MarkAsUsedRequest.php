<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkAsUsedRequest extends FormRequest
{
    public function authorize(): bool
    {
         return true;
    }

    public function rules(): array
    {
        return [
            'uuid' => 'required|string',
            'alianza_id' => 'required|integer|exists:alianzas,alianza_id',
        ];
    }
}
