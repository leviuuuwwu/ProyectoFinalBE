<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Servicio;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->rol !== 'admin') {
            return response()->json([
                'message' => 'no tienes permiso para ver este dashboard.'
            ], 403);
        }

        $citasHoy = Cita::whereDate('fecha_hora', now()->toDateString())->count();

        return response()->json([
            'message' => 'dashboard cargado.',
            'data' => [
                'citas_hoy' => $citasHoy,
                'citas_totales' => Cita::count(),
                'citas_atendidas' => Cita::where('estado', 'Atendida')->count(),
                'citas_canceladas' => Cita::where('estado', 'Cancelada')->count(),
                'citas_programadas' => Cita::where('estado', 'Programada')->count(),
                'especialidades_activas' => Especialidad::where('activo', true)->count(),
                'servicios_activos' => Servicio::where('activo', true)->count(),
                'ingresos' => 0,
            ],
        ]);
    }
}

