<?php

namespace App\Http\Controllers\Api\Profesional;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfesionalHorarioRequest;
use App\Models\ProfesionalHorario;
use Illuminate\Support\Facades\DB;

class StoreProfesionalHorarioController extends Controller
{
    public function __invoke(StoreProfesionalHorarioRequest $request)
    {
        $user = $request->user();

        if ($user->rol !== 'medico') {
            return response()->json(['message' => 'Solo los profesionales pueden definir horario.'], 403);
        }

        DB::transaction(function () use ($request, $user): void {
            ProfesionalHorario::where('user_id', $user->id)->delete();

            foreach ($request->input('dias') as $dia) {
                ProfesionalHorario::create([
                    'user_id' => $user->id,
                    'dia_semana' => (int) $dia['dia_semana'],
                    'hora_inicio' => $dia['hora_inicio'],
                    'hora_fin' => $dia['hora_fin'],
                    'intervalo_minutos' => (int) $request->input('intervalo_minutos'),
                ]);
            }
        });

        $horarios = ProfesionalHorario::where('user_id', $user->id)
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json([
            'message' => 'Horario base guardado.',
            'data' => $horarios,
        ], 201);
    }
}
