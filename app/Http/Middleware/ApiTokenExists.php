<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Native\Mobile\Facades\SecureStorage;


class ApiTokenExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if data exists in SecureStorage
        $storedData = SecureStorage::get('data');

        if (!$storedData) {
            return redirect()->route('home');
        }

        // Decode and check for token
        $data = json_decode($storedData, true);

        if (!isset($data['token']) || empty($data['token'])) {
            return redirect()->route('home');
        }

        // Verificar expiración de 365 días
        if (!isset($data['expires_at']) || now()->timestamp > $data['expires_at']) {
            SecureStorage::forget('data');
            return redirect()->route('home');
        }

        return $next($request);
    }
}