<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReservaWebController extends Controller
{
    private $apiUrl = 'http://127.0.0.1:8000/api/v1';

    public function index()
    {
        $response = Http::withToken(session('user_token'))->get("{$this->apiUrl}/reservas");
        $reservas = $response->successful() ? $response->json() : [];
        return view('reservas.index', compact('reservas'));
    }

    public function store(Request $request)
    {
        $response = Http::withToken(session('user_token'))
            ->post("{$this->apiUrl}/reservas", $request->all());

        if ($response->successful()) {
            return redirect()->route('reservas.index')->with('success', 'Reserva creada exitosamente');
        }

        $mensaje = $response->json('message', 'Error al crear la reserva');
        return back()->with('error', $mensaje)->withInput();
    }

    public function cancelar($id)
    {
        $response = Http::withToken(session('user_token'))
            ->patch("{$this->apiUrl}/reservas/{$id}/cancelar");

        if ($response->successful()) {
            return redirect()->route('reservas.index')->with('success', 'Reserva cancelada exitosamente');
        }

        return back()->with('error', 'Error al cancelar la reserva');
    }

    public function confirmar($id)
    {
        $response = Http::withToken(session('user_token'))
            ->patch("{$this->apiUrl}/reservas/{$id}/confirmar");

        if ($response->successful()) {
            return redirect()->route('reservas.index')->with('success', 'Reserva confirmada exitosamente');
        }

        return back()->with('error', 'Error al confirmar la reserva');
    }
}