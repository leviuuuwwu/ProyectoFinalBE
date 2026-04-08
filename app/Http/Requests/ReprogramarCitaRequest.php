<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReprogramarCitaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nueva_fecha_hora' => 'required|date|after:now',
        ];
    }
}