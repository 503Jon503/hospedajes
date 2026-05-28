<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Notificacion;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show($reservaId)
    {
        $reserva = Reserva::with(['cliente', 'hospedaje.propietario'])->findOrFail($reservaId);
        $userId  = session('user_data.id');

        // Solo el cliente o el propietario de esa reserva pueden ver el chat
        $esDueño = $reserva->hospedaje->user_id === $userId;
        $esCliente = $reserva->user_id === $userId;
        $esAdmin = session('user_data.rol') === 'admin';

        if (!$esDueño && !$esCliente && !$esAdmin) {
            return redirect()->route('reservas.index')->with('error', 'No autorizado');
        }

        // Marcar mensajes como leídos
        Mensaje::where('reserva_id', $reservaId)
            ->where('destinatario_id', $userId)
            ->where('leido', false)
            ->update(['leido' => true]);

        $mensajes = Mensaje::with('remitente')
            ->where('reserva_id', $reservaId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.show', compact('reserva', 'mensajes'));
    }

    public function enviar(Request $request, $reservaId)
    {
        $request->validate([
            'mensaje' => 'required|string|max:1000',
        ], [
            'mensaje.required' => 'El mensaje no puede estar vacío.',
            'mensaje.max'      => 'El mensaje no puede tener más de 1000 caracteres.',
        ]);

        $reserva     = Reserva::with(['hospedaje'])->findOrFail($reservaId);
        $remitenteId = session('user_data.id');

        // Determinar destinatario
        if ($remitenteId === $reserva->user_id) {
            $destinatarioId = $reserva->hospedaje->user_id;
        } else {
            $destinatarioId = $reserva->user_id;
        }

        Mensaje::create([
            'reserva_id'      => $reservaId,
            'remitente_id'    => $remitenteId,
            'destinatario_id' => $destinatarioId,
            'mensaje'         => $request->mensaje,
            'leido'           => false,
        ]);

        // Notificar al destinatario
        Notificacion::enviar(
            $destinatarioId,
            '💬 Nuevo mensaje',
            session('user_data.nombre') . ' te envió un mensaje sobre la reserva en ' . $reserva->hospedaje->nombre,
            'info',
            route('chat.show', $reservaId)
        );

        return back()->with('success', 'Mensaje enviado.');
    }
}