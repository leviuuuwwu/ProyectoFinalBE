<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\ProfesionalBloqueo;
use App\Models\ProfesionalHorario;
use App\Models\User;
use Carbon\Carbon;

class DisponibilidadCalculoService
{
    public function calcular(User $medico, Carbon $fecha): array
    {
        $dia = (int) $fecha->isoWeekday();

        if ($this->diaEstaBloqueado($medico, $fecha)) {
            return [];
        }

        $intervalos = ProfesionalHorario::query()
            ->where('user_id', $medico->id)
            ->where('dia_semana', $dia)
            ->orderBy('hora_inicio')
            ->get();

        if ($intervalos->isEmpty()) {
            return [];
        }

        $citas = Cita::query()
            ->where('medico_id', $medico->id)
            ->whereDate('fecha_hora', $fecha->format('Y-m-d'))
            ->whereIn('estado', ['Programada', 'Reprogramada'])
            ->get();

        $slots = [];

        foreach ($intervalos as $intervalo) {
            $inicio = Carbon::parse($fecha->format('Y-m-d').' '.$intervalo->hora_inicio, config('app.timezone'));
            $fin = Carbon::parse($fecha->format('Y-m-d').' '.$intervalo->hora_fin, config('app.timezone'));
            $step = (int) $intervalo->intervalo_minutos;

            if ($step <= 0 || $fin->lte($inicio)) {
                continue;
            }

            for ($t = $inicio->copy(); $t->copy()->addMinutes($step)->lte($fin); $t->addMinutes($step)) {
                $slotStart = $t->copy();
                $slotEnd = $t->copy()->addMinutes($step);

                if ($this->slotLibre($slotStart, $slotEnd, $citas)) {
                    $slots[] = $slotStart->format('Y-m-d H:i:s');
                }
            }
        }

        sort($slots);

        return array_values(array_unique($slots));
    }

    private function diaEstaBloqueado(User $medico, Carbon $fecha): bool
    {
        $dia = $fecha->format('Y-m-d');

        return ProfesionalBloqueo::query()
            ->where('user_id', $medico->id)
            ->where(function ($q) use ($dia) {
                $q->where(function ($q2) use ($dia) {
                    $q2->whereNull('fecha_fin')
                        ->whereDate('fecha_inicio', $dia);
                })->orWhere(function ($q2) use ($dia) {
                    $q2->whereNotNull('fecha_fin')
                        ->whereDate('fecha_inicio', '<=', $dia)
                        ->whereDate('fecha_fin', '>=', $dia);
                });
            })
            ->exists();
    }

    private function slotLibre(Carbon $slotStart, Carbon $slotEnd, $citas): bool
    {
        foreach ($citas as $cita) {
            $citaStart = $cita->fecha_hora->copy()->timezone(config('app.timezone'));
            $citaEnd = $citaStart->copy()->addMinutes((int) $cita->duracion_minutos);

            if ($slotStart->lt($citaEnd) && $slotEnd->gt($citaStart)) {
                return false;
            }
        }

        return true;
    }
}
