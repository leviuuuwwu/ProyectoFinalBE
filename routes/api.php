<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\ProfileController;

use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\ServicioController;

use App\Http\Controllers\Api\Profesional\ProfesionalDisponibilidadController;
use App\Http\Controllers\Api\Profesional\ShowProfesionalHorarioController;
use App\Http\Controllers\Api\Profesional\StoreProfesionalBloqueoController;
use App\Http\Controllers\Api\Profesional\StoreProfesionalHorarioController;

// RUTAS PÚBLICAS
Route::post('/auth/register', RegisterController::class);
Route::post('/auth/login', LoginController::class);

Route::get('/especialidades', [EspecialidadController::class, 'index']);
Route::get('/servicios', [ServicioController::class, 'index']);

Route::get('/profesionales/{profesional}/horarios', ShowProfesionalHorarioController::class);
Route::get('/profesionales/{profesional}/disponibilidad', ProfesionalDisponibilidadController::class);

// RUTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', ProfileController::class);
    Route::post('/auth/logout', LogoutController::class);

    Route::post('/profesionales/horarios', StoreProfesionalHorarioController::class);
    Route::post('/profesionales/bloqueos', StoreProfesionalBloqueoController::class);
});