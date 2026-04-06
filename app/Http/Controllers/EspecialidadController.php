<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index()
    {
        // Trae todas las especialidades activas y servicios
        $especialidades = Especialidad::where('activo', true)
                                      ->with('servicios')
                                      ->get();

        return response()->json([
            'data' => $especialidades
        ]);
    }
}