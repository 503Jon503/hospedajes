<?php

namespace App\Http\Controllers;

use App\Models\Hospedaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HospedajeWebController extends Controller
{
    private function formatHospedaje($h)
    {
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
            'calificaciones'        => $h->calificaciones->map(fn($c) => [
                'puntuacion'  => $c->puntuacion,
                'comentario'  => $c->comentario,
                'creado_en'   => $c->created_at->format('d/m/Y'),
                'cliente'     => ['nombre' => $c->cliente->nombre, 'apellido' => $c->cliente->apellido],
            ]),
            'creado_en'             => $h->created_at->format('d/m/Y'),
        ];
    }

    public function index(Request $request)
    {
        $query = Hospedaje::with(['propietario', 'calificaciones'])->disponible();

        if ($request->buscar) {
            $query->where('nombre', 'like', "%{$request->buscar}%");
        }
        if ($request->tipo) {
            $query->filtrarPorTipo($request->tipo);
        }
        if ($request->ubicacion) {
            $query->filtrarPorUbicacion($request->ubicacion);
        }

        $data = $query->orderBy('created_at', 'desc')->get()->map(fn($h) => $this->formatHospedaje($h))->toArray();
        $hospedajes = ['data' => $data];
        return view('hospedajes.index', compact('hospedajes'));
    }

    public function show($id)
    {
        $h = Hospedaje::with(['propietario', 'calificaciones.cliente'])->findOrFail($id);
        $hospedaje = $this->formatHospedaje($h);
        return view('hospedajes.show', compact('hospedaje'));
    }

    public function misHospedajes()
    {
        $token = session('user_token');
        $userData = session('user_data');
        $data = Hospedaje::with(['calificaciones'])
            ->where('user_id', $userData['id'])
            ->get()->map(fn($h) => $this->formatHospedaje($h))->toArray();
        $hospedajes = ['data' => $data];
        return view('hospedajes.mis', compact('hospedajes'));
    }

    public function create()
    {
        return view('hospedajes.create');
    }

    public function store(Request $request)
    {
        $response = Http::withToken(session('user_token'))
            ->post('http://127.0.0.1:8000/api/v1/hospedajes', $request->except('imagen'));

        if ($response->successful()) {
            return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje creado exitosamente');
        }

        return back()->with('error', 'Error al crear el hospedaje')->withInput();
    }

    public function edit($id)
    {
        $h = Hospedaje::with(['propietario', 'calificaciones'])->findOrFail($id);
        $hospedaje = $this->formatHospedaje($h);
        return view('hospedajes.edit', compact('hospedaje'));
    }

    public function update(Request $request, $id)
    {
        $response = Http::withToken(session('user_token'))
            ->put("http://127.0.0.1:8000/api/v1/hospedajes/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje actualizado exitosamente');
        }

        return back()->with('error', 'Error al actualizar')->withInput();
    }

    public function destroy($id)
    {
        Http::withToken(session('user_token'))->delete("http://127.0.0.1:8000/api/v1/hospedajes/{$id}");
        return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje eliminado exitosamente');
    }
}