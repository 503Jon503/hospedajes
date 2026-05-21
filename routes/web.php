<?php

use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HospedajeWebController;
use App\Http\Controllers\ReservaWebController;
use Illuminate\Support\Facades\Route;

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// Hospedajes
Route::get('/hospedajes', [HospedajeWebController::class, 'index'])->name('hospedajes.index');
Route::get('/hospedajes/{id}', [HospedajeWebController::class, 'show'])->name('hospedajes.show');

// Rutas protegidas
Route::middleware('auth.session')->group(function () {
    // Hospedajes (propietario)
    Route::get('/mis-hospedajes', [HospedajeWebController::class, 'misHospedajes'])->name('hospedajes.mis');
    Route::get('/hospedajes/crear', [HospedajeWebController::class, 'create'])->name('hospedajes.create');
    Route::post('/hospedajes', [HospedajeWebController::class, 'store'])->name('hospedajes.store');
    Route::get('/hospedajes/{id}/editar', [HospedajeWebController::class, 'edit'])->name('hospedajes.edit');
    Route::put('/hospedajes/{id}', [HospedajeWebController::class, 'update'])->name('hospedajes.update');
    Route::delete('/hospedajes/{id}', [HospedajeWebController::class, 'destroy'])->name('hospedajes.destroy');

    // Reservas
    Route::get('/reservas', [ReservaWebController::class, 'index'])->name('reservas.index');
    Route::post('/reservas', [ReservaWebController::class, 'store'])->name('reservas.store');
    Route::patch('/reservas/{id}/cancelar', [ReservaWebController::class, 'cancelar'])->name('reservas.cancelar');
    Route::patch('/reservas/{id}/confirmar', [ReservaWebController::class, 'confirmar'])->name('reservas.confirmar');
});