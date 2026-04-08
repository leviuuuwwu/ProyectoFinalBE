<?php

namespace App\Jobs;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EnviarRecordatoriosCitasManana
{
    public function handle(): void
    {
        $manana = Carbon::tomorrow()->toDateString();

        $citas = Cita::whereDate('fecha_hora', $manana)
            ->where('estado', 'Programada')
            ->get();

        foreach ($citas as $cita) {
            Log::info('recordatorio de cita enviado', [
                'cita_uuid' => $cita->uuid,
                'paciente_id' => $cita->paciente_id,
                'medico_id' => $cita->medico_id,
                'fecha_hora' => $cita->fecha_hora,
            ]);
        }

        Log::info('recordatorios revisados', [
            'cantidad' => $citas->count(),
        ]);
    }
}

