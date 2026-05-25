<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HospedajeWebController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReservaWebController;
use App\Models\Calificacion;
use Illuminate\Http\Request;
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

// Rutas protegidas
Route::middleware('auth.session')->group(function () {

    // Hospedajes
    Route::get('/hospedajes/crear', [HospedajeWebController::class, 'create'])->name('hospedajes.create');
    Route::post('/hospedajes', [HospedajeWebController::class, 'store'])->name('hospedajes.store');
    Route::get('/mis-hospedajes', [HospedajeWebController::class, 'misHospedajes'])->name('hospedajes.mis');
    Route::get('/hospedajes/{id}/editar', [HospedajeWebController::class, 'edit'])->name('hospedajes.edit');
    Route::put('/hospedajes/{id}', [HospedajeWebController::class, 'update'])->name('hospedajes.update');
    Route::delete('/hospedajes/{id}', [HospedajeWebController::class, 'destroy'])->name('hospedajes.destroy');

    // Reservas
    Route::get('/reservas', [ReservaWebController::class, 'index'])->name('reservas.index');
    Route::post('/reservas', [ReservaWebController::class, 'store'])->name('reservas.store');
    Route::patch('/reservas/{id}/cancelar', [ReservaWebController::class, 'cancelar'])->name('reservas.cancelar');
    Route::patch('/reservas/{id}/confirmar', [ReservaWebController::class, 'confirmar'])->name('reservas.confirmar');

    // Pagos
    Route::get('/pagos/{reserva}/checkout', [PagoController::class, 'checkout'])->name('pagos.checkout');
    Route::post('/pagos/{reserva}/procesar', [PagoController::class, 'procesar'])->name('pagos.procesar');
    Route::get('/pagos/{reserva}/exitoso', [PagoController::class, 'exitoso'])->name('pagos.exitoso');
    Route::patch('/pagos/{reserva}/confirmar-llegada', [PagoController::class, 'confirmarLlegada'])->name('pagos.confirmarLlegada');
    Route::patch('/pagos/{reserva}/liberar', [PagoController::class, 'liberar'])->name('pagos.liberar');

    // Calificaciones
    Route::post('/hospedajes/{id}/calificaciones', function (Request $request, $id) {
        $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $yaCalifico = Calificacion::where('user_id', session('user_data.id'))
            ->where('hospedaje_id', $id)
            ->exists();

        if ($yaCalifico) {
            return back()->with('error', 'Ya calificaste este hospedaje');
        }

        Calificacion::create([
            'user_id'      => session('user_data.id'),
            'hospedaje_id' => $id,
            'puntuacion'   => $request->puntuacion,
            'comentario'   => $request->comentario,
        ]);

        return back()->with('success', '¡Calificación enviada exitosamente!');
    })->name('calificaciones.store');

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

// Show hospedaje (va al final)
Route::get('/hospedajes/{id}', [HospedajeWebController::class, 'show'])->name('hospedajes.show');