<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'medico_id' => 'required|exists:users,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha_hora' => 'required|date|after:now',
            'motivo' => 'nullable|string|max:255',
        ];
    }
}
