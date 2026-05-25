<?php

namespace App\Http\Controllers;

use App\Models\FotoHospedaje;
use App\Models\Hospedaje;
use App\Models\Reserva;
use Illuminate\Http\Request;

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
            'fotos'                 => $h->fotos->map(fn($f) => asset('storage/' . $f->ruta))->toArray(),
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
        $query = Hospedaje::with(['propietario', 'calificaciones', 'fotos'])->disponible();

        if ($request->buscar) {
            $query->where('nombre', 'like', "%{$request->buscar}%");
        }
        if ($request->tipo) {
            $query->filtrarPorTipo($request->tipo);
        }
        if ($request->ubicacion) {
            $query->filtrarPorUbicacion($request->ubicacion);
        }
        if ($request->precio_min && $request->precio_max) {
            $query->filtrarPorPrecio($request->precio_min, $request->precio_max);
        }

        $data = $query->orderBy('created_at', 'desc')->get()->map(fn($h) => $this->formatHospedaje($h))->toArray();
        $hospedajes = ['data' => $data];
        return view('hospedajes.index', compact('hospedajes'));
    }

    public function show($id)
    {
        $h = Hospedaje::with(['propietario', 'calificaciones.cliente', 'fotos'])->findOrFail($id);
        $hospedaje = $this->formatHospedaje($h);

        $reservas = Reserva::where('hospedaje_id', $id)
            ->where('estado', '!=', 'cancelada')
            ->get(['fecha_inicio', 'fecha_fin']);

        $fechasOcupadas = [];
        foreach ($reservas as $reserva) {
            $fechasOcupadas[] = [
                'from' => $reserva->fecha_inicio->format('Y-m-d'),
                'to'   => $reserva->fecha_fin->format('Y-m-d'),
            ];
        }

        $puedeCalificar = false;
        if (session('user_token') && session('user_data.rol') === 'cliente') {
            $puedeCalificar = Reserva::where('user_id', session('user_data.id'))
                ->where('hospedaje_id', $id)
                ->where('estado', 'confirmada')
                ->where('fecha_fin', '<=', now()->format('Y-m-d'))
                ->exists();
        }

        return view('hospedajes.show', compact('hospedaje', 'fechasOcupadas', 'puedeCalificar'));
    }

    public function misHospedajes()
    {
        $userData = session('user_data');
        $data = Hospedaje::with(['calificaciones', 'fotos'])
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
        $data = $request->validate([
            'nombre'       => 'required|string|max:150',
            'tipo'         => 'required|in:hotel,rancho,casa,apartamento',
            'descripcion'  => 'required|string',
            'ubicacion'    => 'required|string|max:200',
            'departamento' => 'required|string|max:100',
            'precio_noche' => 'required|numeric|min:1',
            'capacidad'    => 'required|integer|min:1',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:5120',
            'fotos.*'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:5120',
            'estado'       => 'nullable|in:disponible,no_disponible',
        ]);

        $data['user_id'] = session('user_data.id');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('hospedajes', 'public');
        }

        $hospedaje = Hospedaje::create($data);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $index => $foto) {
                $ruta = $foto->store('hospedajes/fotos', 'public');
                FotoHospedaje::create([
                    'hospedaje_id' => $hospedaje->id,
                    'ruta'         => $ruta,
                    'orden'        => $index,
                ]);
            }
        }

        return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje creado exitosamente');
    }

    public function edit($id)
    {
        $h = Hospedaje::with(['propietario', 'calificaciones', 'fotos'])->findOrFail($id);
        $hospedaje = $this->formatHospedaje($h);
        $fotos = FotoHospedaje::where('hospedaje_id', $id)->get();
        return view('hospedajes.edit', compact('hospedaje', 'fotos'));
    }

    public function update(Request $request, $id)
    {
        $hospedaje = Hospedaje::findOrFail($id);

        $data = $request->validate([
            'nombre'       => 'sometimes|string|max:150',
            'tipo'         => 'sometimes|in:hotel,rancho,casa,apartamento',
            'descripcion'  => 'sometimes|string',
            'ubicacion'    => 'sometimes|string|max:200',
            'departamento' => 'sometimes|string|max:100',
            'precio_noche' => 'sometimes|numeric|min:1',
            'capacidad'    => 'sometimes|integer|min:1',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:5120',
            'fotos.*'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:5120',
            'estado'       => 'sometimes|in:disponible,no_disponible',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('hospedajes', 'public');
        }

        $hospedaje->update($data);

        if ($request->hasFile('fotos')) {
            $totalFotos = FotoHospedaje::where('hospedaje_id', $id)->count();
            foreach ($request->file('fotos') as $index => $foto) {
                $ruta = $foto->store('hospedajes/fotos', 'public');
                FotoHospedaje::create([
                    'hospedaje_id' => $hospedaje->id,
                    'ruta'         => $ruta,
                    'orden'        => $totalFotos + $index,
                ]);
            }
        }

        return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje actualizado exitosamente');
    }

    public function eliminarFoto($id)
    {
        $foto = FotoHospedaje::findOrFail($id);
        \Storage::disk('public')->delete($foto->ruta);
        $foto->delete();
        return back()->with('success', 'Foto eliminada exitosamente');
    }

    public function destroy($id)
    {
        $hospedaje = Hospedaje::findOrFail($id);
        $hospedaje->delete();
        return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje eliminado exitosamente');
    }
}