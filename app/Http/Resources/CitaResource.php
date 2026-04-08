<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CitaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->uuid,
            'paciente_id' => $this->paciente_id,
            'medico_id' => $this->medico_id,
            'fecha_hora' => $this->fecha_hora,
            'estado' => $this->estado,
            'motivo' => $this->motivo,
            'notas' => $this->notas,
        ];
    }
}
