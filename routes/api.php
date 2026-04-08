<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\ProfileController;

use App\Http\Controllers\Api\Admin\UsuarioController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\ServicioController;

// RUTAS PÚBLICAS
Route::post('/auth/register', RegisterController::class);
Route::post('/auth/login', LoginController::class);

Route::get('/especialidades', [EspecialidadController::class, 'index']);
Route::get('/servicios', [ServicioController::class, 'index']);
Route::get('/profesionales', [ProfesionalController::class, 'index']);

// RUTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', ProfileController::class);
    Route::post('/auth/logout', LogoutController::class);
});

// ADMIN
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::put('/usuarios/{user}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{user}', [UsuarioController::class, 'destroy']);
    Route::post('/usuarios/{user}/roles', [UsuarioController::class, 'syncRoles']);
});