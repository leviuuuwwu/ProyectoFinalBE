<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgregarNotasCitaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'notas' => 'nullable|string|max:4000|required_without:receta',
            'receta' => 'nullable|string|max:4000|required_without:notas',
        ];
    }
}

