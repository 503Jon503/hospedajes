<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CalificacionController;
use App\Http\Controllers\API\HospedajeController;
use App\Http\Controllers\API\ReservaController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Hospedajes públicos
    Route::get('/hospedajes', [HospedajeController::class, 'index']);
    Route::get('/hospedajes/{hospedaje}', [HospedajeController::class, 'show']);

    // Calificaciones públicas
    Route::get('/hospedajes/{hospedaje}/calificaciones', [CalificacionController::class, 'index']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Hospedajes
        Route::post('/hospedajes', [HospedajeController::class, 'store']);
        Route::put('/hospedajes/{hospedaje}', [HospedajeController::class, 'update']);
        Route::delete('/hospedajes/{hospedaje}', [HospedajeController::class, 'destroy']);
        Route::get('/mis-hospedajes', [HospedajeController::class, 'miHospedajes']);

        // Reservas
        Route::get('/reservas', [ReservaController::class, 'index']);
        Route::post('/reservas', [ReservaController::class, 'store']);
        Route::get('/reservas/{reserva}', [ReservaController::class, 'show']);
        Route::patch('/reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar']);
        Route::patch('/reservas/{reserva}/confirmar', [ReservaController::class, 'confirmar']);

        // Calificaciones
        Route::post('/hospedajes/{hospedaje}/calificaciones', [CalificacionController::class, 'store']);
        Route::delete('/calificaciones/{calificacion}', [CalificacionController::class, 'destroy']);

        // Usuarios (admin)
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});