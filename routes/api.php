<?php

use App\Http\Controllers\CitaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/citas', [CitaController::class , 'index']);
    Route::post('/citas', [CitaController::class , 'store']);
    Route::get('/citas/{cita:uuid}', [CitaController::class , 'show']);
    Route::patch('/citas/{cita:uuid}/cancelar', [CitaController::class , 'cancelar']);
    Route::patch('/citas/{cita:uuid}/reprogramar', [CitaController::class , 'reprogramar']);
    Route::patch('/citas/{cita:uuid}/completar', [CitaController::class , 'completar']);
});
