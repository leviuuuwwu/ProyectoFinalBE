<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\ProfileController;

use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\AdminDashboardController;

// RUTAS PÚBLICAS
Route::post('/auth/register', RegisterController::class);
Route::post('/auth/login', LoginController::class);

Route::get('/especialidades', [EspecialidadController::class , 'index']);
Route::get('/servicios', [ServicioController::class , 'index']);

// RUTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', ProfileController::class);
    Route::post('/auth/logout', LogoutController::class);

    Route::get('/citas', [CitaController::class , 'index']);
    Route::get('/pacientes/{paciente}/historial', [CitaController::class , 'historial']);
    Route::get('/admin/dashboard', AdminDashboardController::class);
    Route::post('/citas', [CitaController::class , 'store']);
    Route::get('/citas/{cita:uuid}', [CitaController::class , 'show']);
    Route::post('/citas/{cita:uuid}/notas', [CitaController::class , 'notas']);
    Route::patch('/citas/{cita:uuid}/cancelar', [CitaController::class , 'cancelar']);
    Route::patch('/citas/{cita:uuid}/reprogramar', [CitaController::class , 'reprogramar']);
    Route::patch('/citas/{cita:uuid}/completar', [CitaController::class , 'completar']);
});
