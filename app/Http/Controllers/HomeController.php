<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    private $apiUrl = 'http://127.0.0.1:8000/api/v1';

    public function index(Request $request)
    {
        $response = Http::get("{$this->apiUrl}/hospedajes", [
            'buscar'   => $request->buscar,
            'tipo'     => $request->tipo,
            'ubicacion'=> $request->ubicacion,
        ]);

        $hospedajes = $response->successful() ? $response->json('data') : [];

        return view('home', compact('hospedajes'));
    }
}