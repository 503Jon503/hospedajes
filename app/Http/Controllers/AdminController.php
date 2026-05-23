<?php

namespace App\Http\Controllers;

use App\Models\Hospedaje;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsuarios   = User::count();
        $totalHospedajes = Hospedaje::count();
        $totalReservas   = Reserva::count();
        $reservasPendientes = Reserva::where('estado', 'pendiente')->count();

        return view('admin.index', compact(
            'totalUsuarios',
            'totalHospedajes',
            'totalReservas',
            'reservasPendientes'
        ));
    }

    public function usuarios()
    {
        $usuarios = User::orderBy('created_at', 'desc')->get();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function cambiarRol(Request $request, User $user)
    {
        $request->validate([
            'rol' => 'required|in:admin,propietario,cliente',
        ]);

        $user->update(['rol' => $request->rol]);
        return redirect()->route('admin.usuarios')->with('success', 'Rol actualizado exitosamente');
    }

    public function eliminarUsuario(User $user)
    {
        $user->delete();
        return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado exitosamente');
    }

    public function hospedajes()
    {
        $hospedajes = Hospedaje::with('propietario')->orderBy('created_at', 'desc')->get();
        return view('admin.hospedajes', compact('hospedajes'));
    }

    public function eliminarHospedaje(Hospedaje $hospedaje)
    {
        $hospedaje->delete();
        return redirect()->route('admin.hospedajes')->with('success', 'Hospedaje eliminado exitosamente');
    }

    public function reservas()
    {
        $reservas = Reserva::with(['cliente', 'hospedaje'])->orderBy('created_at', 'desc')->get();
        return view('admin.reservas', compact('reservas'));
    }
}