<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Roles;

class ProfesionalController extends Controller
{
    public function index()
    {
        $profesionales = User::role(Roles::PROFESIONAL)
            ->where('activo', true)
            ->with('roles')
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'data' => $profesionales,
        ]);
    }
}
