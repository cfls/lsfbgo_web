<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenExists
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        $data = session('data');

        if (!$data) {
            return redirect('/');
        }

        if (empty($data['token'])) {
            return redirect('/');
        }

        // Verificar expiración de 365 días
        if (!isset($data['expires_at']) || now()->timestamp > $data['expires_at']) {
            session()->forget(['data', 'token']);
            return redirect('/');
        }

        return $next($request);
    }
}