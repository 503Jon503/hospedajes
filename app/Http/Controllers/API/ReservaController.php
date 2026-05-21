<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Models\Hospedaje;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->esAdmin()) {
            $reservas = Reserva::with(['cliente', 'hospedaje'])->paginate(15);
        } elseif ($user->esPropietario()) {
            $reservas = Reserva::with(['cliente', 'hospedaje'])
                ->whereHas('hospedaje', fn($q) => $q->where('user_id', $user->id))
                ->paginate(15);
        } else {
            $reservas = Reserva::with(['cliente', 'hospedaje'])
                ->where('user_id', $user->id)
                ->paginate(15);
        }

        return ReservaResource::collection($reservas);
    }

    public function store(StoreReservaRequest $request)
    {
        $hospedaje = Hospedaje::findOrFail($request->hospedaje_id);

        $conflicto = Reserva::where('hospedaje_id', $hospedaje->id)
            ->where('estado', '!=', 'cancelada')
            ->where(function ($q) use ($request) {
                $q->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                  ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin]);
            })->exists();

        if ($conflicto) {
            return response()->json([
                'message' => 'El hospedaje no está disponible en esas fechas',
            ], 422);
        }

        $dias  = \Carbon\Carbon::parse($request->fecha_inicio)->diffInDays($request->fecha_fin);
        $total = $dias * $hospedaje->precio_noche;

        $reserva = Reserva::create([
            'user_id'      => auth()->id(),
            'hospedaje_id' => $request->hospedaje_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
            'num_personas' => $request->num_personas,
            'total'        => $total,
            'notas'        => $request->notas,
            'estado'       => 'pendiente',
        ]);

        return response()->json([
            'message' => 'Reserva creada exitosamente',
            'reserva' => new ReservaResource($reserva->load(['cliente', 'hospedaje'])),
        ], 201);
    }

    public function show(Reserva $reserva)
    {
        $this->authorize('view', $reserva);
        return new ReservaResource($reserva->load(['cliente', 'hospedaje']));
    }

    public function cancelar(Reserva $reserva)
    {
        $this->authorize('update', $reserva);

        if ($reserva->estado === 'cancelada') {
            return response()->json(['message' => 'La reserva ya está cancelada'], 422);
        }

        $reserva->update(['estado' => 'cancelada']);

        return response()->json(['message' => 'Reserva cancelada exitosamente']);
    }

    public function confirmar(Reserva $reserva)
    {
        if (!auth()->user()->esPropietario() && !auth()->user()->esAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $reserva->update(['estado' => 'confirmada']);

        return response()->json(['message' => 'Reserva confirmada exitosamente']);
    }
}