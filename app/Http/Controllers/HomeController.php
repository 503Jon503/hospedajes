<?php

namespace App\Http\Controllers;

use App\Models\Hospedaje;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Hospedaje::with(['propietario', 'calificaciones'])->disponible();

        if ($request->buscar) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('descripcion', 'like', "%{$request->buscar}%");
        }

        if ($request->tipo) {
            $query->filtrarPorTipo($request->tipo);
        }

        if ($request->ubicacion) {
            $query->filtrarPorUbicacion($request->ubicacion);
        }

        $hospedajes = $query->orderBy('created_at', 'desc')->get()->map(function ($h) {
            return [
                'id'                    => $h->id,
                'nombre'                => $h->nombre,
                'tipo'                  => $h->tipo,
                'descripcion'           => $h->descripcion,
                'ubicacion'             => $h->ubicacion,
                'departamento'          => $h->departamento,
                'precio_noche'          => $h->precio_noche,
                'capacidad'             => $h->capacidad,
                'imagen'                => $h->imagen ? asset('storage/' . $h->imagen) : null,
                'estado'                => $h->estado,
                'promedio_calificacion' => round($h->promedioCalificacion(), 1),
                'total_calificaciones'  => $h->calificaciones->count(),
                'propietario'           => $h->propietario,
                'creado_en'             => $h->created_at->format('d/m/Y'),
            ];
        })->toArray();

        return view('home', compact('hospedajes'));
    }
}