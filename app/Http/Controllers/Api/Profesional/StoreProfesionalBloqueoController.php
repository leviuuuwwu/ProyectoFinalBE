<?php

namespace App\Http\Controllers\Api\Profesional;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfesionalBloqueoRequest;
use App\Models\ProfesionalBloqueo;

class StoreProfesionalBloqueoController extends Controller
{
    public function __invoke(StoreProfesionalBloqueoRequest $request)
    {
        $user = $request->user();

        if ($user->rol !== 'medico') {
            return response()->json(['message' => 'Solo los profesionales pueden registrar bloqueos.'], 403);
        }

        $bloqueo = ProfesionalBloqueo::create([
            'user_id' => $user->id,
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
            'motivo' => $request->input('motivo'),
        ]);

        return response()->json([
            'message' => 'Bloqueo registrado.',
            'data' => $bloqueo,
        ], 201);
    }
}
