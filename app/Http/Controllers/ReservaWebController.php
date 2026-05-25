<?php

namespace App\Http\Controllers;

use App\Models\Hospedaje;
use App\Models\Reserva;
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
            'pago_estado'                => $r->pago_estado,
            'cliente_confirmo_llegada'   => $r->cliente_confirmo_llegada,
            'fecha_confirmacion_llegada' => $r->fecha_confirmacion_llegada ? $r->fecha_confirmacion_llegada->format('d/m/Y H:i') : null,
            'notas'                      => $r->notas,
            'cliente'                    => $r->cliente ? ['nombre' => $r->cliente->nombre, 'apellido' => $r->cliente->apellido] : null,
            'hospedaje'                  => $r->hospedaje ? ['nombre' => $r->hospedaje->nombre, 'ubicacion' => $r->hospedaje->ubicacion] : null,
        ];
    }

    public function index()
    {
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

        $hospedaje = Hospedaje::findOrFail($request->hospedaje_id);

        $fechaInicio = \Carbon\Carbon::parse($request->fecha_inicio)->format('Y-m-d');
        $fechaFin    = \Carbon\Carbon::parse($request->fecha_fin)->format('Y-m-d');

        $conflicto = Reserva::where('hospedaje_id', $hospedaje->id)
            ->where('estado', '!=', 'cancelada')
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
            'user_id'      => session('user_data.id'),
            'hospedaje_id' => $request->hospedaje_id,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
            'num_personas' => $request->num_personas,
            'total'        => $total,
            'notas'        => $request->notas,
            'estado'       => 'pendiente',
            'pago_estado'  => 'pendiente',
        ]);

        return redirect()->route('pagos.checkout', $reserva->id)
            ->with('success', '¡Reserva creada! Por favor realiza el pago para confirmarla.');
    }

    public function cancelar($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update(['estado' => 'cancelada']);
        return redirect()->route('reservas.index')->with('success', 'Reserva cancelada exitosamente');
    }

    public function confirmar($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update(['estado' => 'confirmada']);
        return redirect()->route('reservas.index')->with('success', 'Reserva confirmada exitosamente');
    }
}