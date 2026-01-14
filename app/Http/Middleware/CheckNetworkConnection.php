<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Native\Mobile\Facades\Network;
use Symfony\Component\HttpFoundation\Response;

class CheckNetworkConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $networkStatus = Network::status();

        // Si no hay conexión, redirigir a la pantalla de sin conexión
        if (!$networkStatus || !$networkStatus->connected) {
            // Si la petición es AJAX o espera JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Sin conexión a internet',
                    'message' => 'No se pudo establecer conexión. Por favor, verifica tu conexión e intenta nuevamente.'
                ], 503);
            }

            // Si es una petición normal, redirigir a la vista de sin conexión
            if (!$request->is('sin-conexion')) {
                return redirect()->route('sin-conexion');
            }
        }

        return $next($request);
    }
}
