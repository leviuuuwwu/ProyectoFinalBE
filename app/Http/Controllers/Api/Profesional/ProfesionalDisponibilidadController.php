<?php

namespace App\Http\Controllers\Api\Profesional;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DisponibilidadCalculoService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfesionalDisponibilidadController extends Controller
{
    public function __invoke(Request $request, User $profesional, DisponibilidadCalculoService $calculo)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date_format:Y-m-d'],
        ]);

        if ($profesional->rol !== 'medico') {
            return response()->json(['message' => 'Profesional no encontrado.'], 404);
        }

        $fecha = Carbon::createFromFormat('Y-m-d', $validated['fecha'], config('app.timezone'))->startOfDay();

        $slots = $calculo->calcular($profesional, $fecha);

        return response()->json([
            'fecha' => $validated['fecha'],
            'intervalo_minutos' => $profesional->profesionalHorarios()
                ->where('dia_semana', (int) $fecha->isoWeekday())
                ->value('intervalo_minutos'),
            'data' => $slots,
        ]);
    }
}
