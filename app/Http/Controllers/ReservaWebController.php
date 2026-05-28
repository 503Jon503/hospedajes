<?php

namespace App\Http\Controllers;

use App\Models\Hospedaje;
use App\Models\Notificacion;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Http\Request;

class ReservaWebController extends Controller
{
    private function formatReserva($r)
    {
        return [
            'id'                         => $r->id,
            'fecha_inicio'               => $r->fecha_inicio->format('d/m/Y'),
            'fecha_fin'                  => $r->fecha_fin->format('d/m/Y'),
            'num_personas'               => $r->num_personas,
            'total'                      => $r->total,
            'estado'                     => $r->estado,
            'estado_propietario'         => $r->estado_propietario,
            'pago_estado'                => $r->pago_estado,
            'cliente_confirmo_llegada'   => $r->cliente_confirmo_llegada,
            'fecha_confirmacion_llegada' => $r->fecha_confirmacion_llegada ? $r->fecha_confirmacion_llegada->format('d/m/Y H:i') : null,
            'notas'                      => $r->notas,
            'cliente'                    => $r->cliente ? [
                'id'       => $r->cliente->id,
                'nombre'   => $r->cliente->nombre,
                'apellido' => $r->cliente->apellido,
                'email'    => $r->cliente->email,
                'telefono' => $r->cliente->telefono,
            ] : null,
            'hospedaje'                  => $r->hospedaje ? [
                'id'        => $r->hospedaje->id,
                'nombre'    => $r->hospedaje->nombre,
                'ubicacion' => $r->hospedaje->ubicacion,
            ] : null,
        ];
    }

 public function index()
{
    // Expirar reservas pendientes automáticamente
    Reserva::where('estado_propietario', 'pendiente')
        ->where('estado', 'pendiente')
        ->where('fecha_inicio', '<', now()->format('Y-m-d'))
        ->each(function ($reserva) {
            $reserva->update([
                'estado'             => 'cancelada',
                'estado_propietario' => 'rechazada',
            ]);

            Notificacion::enviar(
                $reserva->user_id,
                '⏰ Reserva expirada',
                'Tu solicitud de reserva en ' . $reserva->hospedaje->nombre . ' expiró porque el propietario no respondió a tiempo.',
                'warning',
                route('reservas.index')
            );
        });

    $user = session('user_data');
    $query = Reserva::with(['cliente', 'hospedaje']);

    if ($user['rol'] === 'admin') {
        $reservas = $query->get();
    } elseif ($user['rol'] === 'propietario') {
        $reservas = $query->whereHas('hospedaje', fn($q) => $q->where('user_id', $user['id']))->get();
    } else {
        $reservas = $query->where('user_id', $user['id'])->get();
    }

    $data = $reservas->map(fn($r) => $this->formatReserva($r))->toArray();
    $reservas = ['data' => $data];
    return view('reservas.index', compact('reservas'));
}

    public function store(Request $request)
    {
        $request->validate([
            'hospedaje_id' => 'required|exists:hospedajes,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
            'num_personas' => 'required|integer|min:1',
        ]);

        $hospedaje = Hospedaje::with('propietario')->findOrFail($request->hospedaje_id);

        $fechaInicio = \Carbon\Carbon::parse($request->fecha_inicio)->format('Y-m-d');
        $fechaFin    = \Carbon\Carbon::parse($request->fecha_fin)->format('Y-m-d');

        $conflicto = Reserva::where('hospedaje_id', $hospedaje->id)
            ->where('estado', '!=', 'cancelada')
            ->where('estado_propietario', '!=', 'rechazada')
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin]);
            })->exists();

        if ($conflicto) {
            return back()->with('error', 'El hospedaje no está disponible en esas fechas');
        }

        $dias  = \Carbon\Carbon::parse($fechaInicio)->diffInDays($fechaFin);
        $total = $dias * $hospedaje->precio_noche;

        $reserva = Reserva::create([
            'user_id'            => session('user_data.id'),
            'hospedaje_id'       => $request->hospedaje_id,
            'fecha_inicio'       => $fechaInicio,
            'fecha_fin'          => $fechaFin,
            'num_personas'       => $request->num_personas,
            'total'              => $total,
            'notas'              => $request->notas,
            'estado'             => 'pendiente',
            'estado_propietario' => 'pendiente',
            'pago_estado'        => 'pendiente',
        ]);

        Notificacion::enviar(
            $hospedaje->user_id,
            '🏠 Nueva solicitud de reserva',
            session('user_data.nombre') . ' ' . session('user_data.apellido') . ' quiere reservar ' . $hospedaje->nombre . ' del ' . \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($fechaFin)->format('d/m/Y'),
            'info',
            route('reservas.index')
        );

        return redirect()->route('reservas.index')
            ->with('success', '¡Solicitud enviada! Espera a que el propietario la acepte.');
    }

    public function aceptar($id)
    {
        $reserva = Reserva::with(['cliente', 'hospedaje'])->findOrFail($id);

        $propietario = User::findOrFail(session('user_data.id'));

        if (!$propietario->cuenta_bancaria || !$propietario->banco) {
            return back()->with('error', '⚠️ Debes registrar tu cuenta bancaria antes de aceptar reservas. Ve a Mi Perfil → Cuenta bancaria para pagos.');
        }

        $reserva->update(['estado_propietario' => 'aceptada']);

        Notificacion::enviar(
            $reserva->user_id,
            '✅ Reserva aceptada',
            'Tu solicitud para ' . $reserva->hospedaje->nombre . ' fue aceptada. Procede a realizar el pago.',
            'success',
            route('pagos.checkout', $reserva->id)
        );

        return back()->with('success', 'Reserva aceptada. El cliente será notificado para que realice el pago.');
    }

    public function rechazar($id)
    {
        $reserva = Reserva::with(['cliente', 'hospedaje'])->findOrFail($id);

        $reserva->update([
            'estado_propietario' => 'rechazada',
            'estado'             => 'cancelada',
        ]);

        Notificacion::enviar(
            $reserva->user_id,
            '❌ Reserva rechazada',
            'Tu solicitud para ' . $reserva->hospedaje->nombre . ' fue rechazada por el propietario.',
            'danger',
            route('reservas.index')
        );

        return back()->with('success', 'Reserva rechazada.');
    }

    public function cancelar($id)
    {
        $reserva = Reserva::with(['hospedaje', 'hospedaje.propietario'])->findOrFail($id);
        $reserva->update(['estado' => 'cancelada']);

        Notificacion::enviar(
            $reserva->hospedaje->user_id,
            '❌ Reserva cancelada por cliente',
            session('user_data.nombre') . ' canceló su reserva en ' . $reserva->hospedaje->nombre,
            'warning',
            route('reservas.index')
        );

        return redirect()->route('reservas.index')->with('success', 'Reserva cancelada exitosamente');
    }

    public function confirmar($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update(['estado' => 'confirmada']);
        return redirect()->route('reservas.index')->with('success', 'Reserva confirmada exitosamente');
    }
}