<?php

namespace App\Http\Middleware;

use App\Services\ApiService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si existe el token en la sesión
        if (!session()->has('data') || empty(session('data.token'))) {
            return redirect()->route('access.login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        return $next($request);
    }
}