<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicio::where('activo', true)->with('especialidad');
        if ($request->has('especialidad_id')) {
            $query->where('especialidad_id', $request->especialidad_id);
        }

        return response()->json([
            'data' => $query->get()
        ]);
    }
}