<?php

use App\Http\Controllers\AdminController;
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

// Hospedajes públicos
Route::get('/hospedajes', [HospedajeWebController::class, 'index'])->name('hospedajes.index');
Route::get('/hospedajes/{id}', [HospedajeWebController::class, 'show'])->name('hospedajes.show');

// Rutas protegidas
Route::middleware('auth.session')->group(function () {

    // Hospedajes
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

    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
        Route::patch('/usuarios/{user}/rol', [AdminController::class, 'cambiarRol'])->name('usuarios.rol');
        Route::delete('/usuarios/{user}', [AdminController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
        Route::get('/hospedajes', [AdminController::class, 'hospedajes'])->name('hospedajes');
        Route::delete('/hospedajes/{hospedaje}', [AdminController::class, 'eliminarHospedaje'])->name('hospedajes.eliminar');
        Route::get('/reservas', [AdminController::class, 'reservas'])->name('reservas');
    });
});