<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalificacionRequest;
use App\Http\Resources\CalificacionResource;
use App\Models\Calificacion;
use App\Models\Hospedaje;

class CalificacionController extends Controller
{
    public function index(Hospedaje $hospedaje)
    {
        $calificaciones = Calificacion::with('cliente')
            ->where('hospedaje_id', $hospedaje->id)
            ->paginate(10);

        return CalificacionResource::collection($calificaciones);
    }

    public function store(StoreCalificacionRequest $request, Hospedaje $hospedaje)
    {
        $yaCalifico = Calificacion::where('user_id', auth()->id())
            ->where('hospedaje_id', $hospedaje->id)
            ->exists();

        if ($yaCalifico) {
            return response()->json([
                'message' => 'Ya has calificado este hospedaje',
            ], 422);
        }

        $calificacion = Calificacion::create([
            'user_id'      => auth()->id(),
            'hospedaje_id' => $hospedaje->id,
            'puntuacion'   => $request->puntuacion,
            'comentario'   => $request->comentario,
        ]);

        return response()->json([
            'message'      => 'Calificación registrada exitosamente',
            'calificacion' => new CalificacionResource($calificacion->load('cliente')),
        ], 201);
    }

    public function destroy(Calificacion $calificacion)
    {
        if (auth()->id() !== $calificacion->user_id && !auth()->user()->esAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $calificacion->delete();

        return response()->json(['message' => 'Calificación eliminada exitosamente']);
    }
}