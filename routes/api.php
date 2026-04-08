<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\ProfileController;

use App\Http\Controllers\Api\Admin\UsuarioController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\ProfesionalController;
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
Route::get('/profesionales', [ProfesionalController::class, 'index']);

Route::get('/profesionales/{profesional}/horarios', ShowProfesionalHorarioController::class);
Route::get('/profesionales/{profesional}/disponibilidad', ProfesionalDisponibilidadController::class);

// RUTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', ProfileController::class);
    Route::post('/auth/logout', LogoutController::class);

    Route::post('/profesionales/horarios', StoreProfesionalHorarioController::class);
    Route::post('/profesionales/bloqueos', StoreProfesionalBloqueoController::class);

    Route::get('/citas', [CitaController::class , 'index']);
    Route::get('/pacientes/{paciente}/historial', [CitaController::class , 'historial']);
    Route::post('/citas', [CitaController::class , 'store']);
    Route::get('/citas/{cita:uuid}', [CitaController::class , 'show']);
    Route::post('/citas/{cita:uuid}/notas', [CitaController::class , 'notas']);
    Route::patch('/citas/{cita:uuid}/cancelar', [CitaController::class , 'cancelar']);
    Route::patch('/citas/{cita:uuid}/reprogramar', [CitaController::class , 'reprogramar']);
    Route::patch('/citas/{cita:uuid}/completar', [CitaController::class , 'completar']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class);
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::put('/usuarios/{user}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{user}', [UsuarioController::class, 'destroy']);
    Route::post('/usuarios/{user}/roles', [UsuarioController::class, 'syncRoles']);
});
