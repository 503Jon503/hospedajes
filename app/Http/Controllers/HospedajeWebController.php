<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HospedajeWebController extends Controller
{
    private $apiUrl = 'http://127.0.0.1:8000/api/v1';

    public function index(Request $request)
    {
        $response = Http::get("{$this->apiUrl}/hospedajes", $request->all());
        $hospedajes = $response->successful() ? $response->json() : [];
        return view('hospedajes.index', compact('hospedajes'));
    }

    public function show($id)
    {
        $response = Http::get("{$this->apiUrl}/hospedajes/{$id}");
        if (!$response->successful()) {
            return redirect()->route('hospedajes.index')->with('error', 'Hospedaje no encontrado');
        }
        $hospedaje = $response->json('data');
        return view('hospedajes.show', compact('hospedaje'));
    }

    public function misHospedajes()
    {
        $response = Http::withToken(session('user_token'))->get("{$this->apiUrl}/mis-hospedajes");
        $hospedajes = $response->successful() ? $response->json() : [];
        return view('hospedajes.mis', compact('hospedajes'));
    }

    public function create()
    {
        return view('hospedajes.create');
    }

    public function store(Request $request)
    {
        $response = Http::withToken(session('user_token'))
            ->attach('imagen', $request->file('imagen') ? file_get_contents($request->file('imagen')) : null, 'imagen.jpg')
            ->post("{$this->apiUrl}/hospedajes", $request->except('imagen'));

        if ($response->successful()) {
            return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje creado exitosamente');
        }

        return back()->with('error', 'Error al crear el hospedaje')->withInput();
    }

    public function edit($id)
    {
        $response = Http::get("{$this->apiUrl}/hospedajes/{$id}");
        $hospedaje = $response->json('data');
        return view('hospedajes.edit', compact('hospedaje'));
    }

    public function update(Request $request, $id)
    {
        $response = Http::withToken(session('user_token'))
            ->put("{$this->apiUrl}/hospedajes/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje actualizado exitosamente');
        }

        return back()->with('error', 'Error al actualizar')->withInput();
    }

    public function destroy($id)
    {
        Http::withToken(session('user_token'))->delete("{$this->apiUrl}/hospedajes/{$id}");
        return redirect()->route('hospedajes.mis')->with('success', 'Hospedaje eliminado exitosamente');
    }
}