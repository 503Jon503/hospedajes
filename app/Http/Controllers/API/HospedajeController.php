<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHospedajeRequest;
use App\Http\Requests\UpdateHospedajeRequest;
use App\Http\Resources\HospedajeResource;
use App\Models\Hospedaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HospedajeController extends Controller
{
    public function index(Request $request)
    {
        $query = Hospedaje::with(['propietario', 'calificaciones'])->disponible();

        if ($request->tipo) {
            $query->filtrarPorTipo($request->tipo);
        }

        if ($request->ubicacion) {
            $query->filtrarPorUbicacion($request->ubicacion);
        }

        if ($request->precio_min && $request->precio_max) {
            $query->filtrarPorPrecio($request->precio_min, $request->precio_max);
        }

        if ($request->buscar) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('descripcion', 'like', "%{$request->buscar}%");
        }

        $hospedajes = $query->orderBy('created_at', 'desc')->paginate(12);

        return HospedajeResource::collection($hospedajes);
    }

    public function store(StoreHospedajeRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('hospedajes', 'public');
        }

        $hospedaje = Hospedaje::create($data);

        return response()->json([
            'message'   => 'Hospedaje creado exitosamente',
            'hospedaje' => new HospedajeResource($hospedaje),
        ], 201);
    }

    public function show(Hospedaje $hospedaje)
    {
        $hospedaje->load(['propietario', 'calificaciones.cliente', 'reservas']);
        return new HospedajeResource($hospedaje);
    }

    public function update(UpdateHospedajeRequest $request, Hospedaje $hospedaje)
    {
        $this->authorize('update', $hospedaje);

        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($hospedaje->imagen) {
                Storage::disk('public')->delete($hospedaje->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('hospedajes', 'public');
        }

        $hospedaje->update($data);

        return response()->json([
            'message'   => 'Hospedaje actualizado exitosamente',
            'hospedaje' => new HospedajeResource($hospedaje),
        ]);
    }

    public function destroy(Hospedaje $hospedaje)
    {
        $this->authorize('delete', $hospedaje);
        $hospedaje->delete();

        return response()->json([
            'message' => 'Hospedaje eliminado exitosamente',
        ]);
    }

    public function miHospedajes(Request $request)
    {
        $hospedajes = Hospedaje::with(['calificaciones', 'reservas'])
            ->where('user_id', auth()->id())
            ->paginate(12);

        return HospedajeResource::collection($hospedajes);
    }
}