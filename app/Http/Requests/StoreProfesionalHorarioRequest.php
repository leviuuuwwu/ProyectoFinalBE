<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProfesionalHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'intervalo_minutos' => ['required', 'integer', 'in:30,60'],
            'dias' => ['required', 'array', 'min:1'],
            'dias.*.dia_semana' => ['required', 'integer', 'min:1', 'max:7'],
            'dias.*.hora_inicio' => ['required', 'string'],
            'dias.*.hora_fin' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $dias = $this->input('dias', []);
            foreach ($dias as $i => $dia) {
                $ini = $dia['hora_inicio'] ?? null;
                $fin = $dia['hora_fin'] ?? null;
                if (! is_string($ini) || ! is_string($fin)) {
                    continue;
                }
                try {
                    $a = Carbon::parse('2000-01-01 '.$ini);
                    $b = Carbon::parse('2000-01-01 '.$fin);
                    if ($b->lte($a)) {
                        $validator->errors()->add("dias.$i.hora_fin", 'La hora de fin debe ser posterior a la de inicio.');
                    }
                } catch (\Throwable) {
                    $validator->errors()->add("dias.$i.hora_inicio", 'Formato de hora inválido.');
                }
            }
        });
    }
}
