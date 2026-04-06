<?php

namespace App\Http\Controllers\Api\Profesional;

use App\Http\Controllers\Controller;
use App\Models\User;

class ShowProfesionalHorarioController extends Controller
{
    public function __invoke(User $profesional)
    {
        if ($profesional->rol !== 'medico') {
            return response()->json(['message' => 'Profesional no encontrado.'], 404);
        }

        $horarios = $profesional->profesionalHorarios()
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json(['data' => $horarios]);
    }
}
