<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_token') || !session()->has('user_data')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para continuar');
        }

        return $next($request);
    }
}