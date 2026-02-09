<?php

namespace App\Http\Middleware;

use App\Services\ApiService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Native\Mobile\Facades\SecureStorage;
use Symfony\Component\HttpFoundation\Response;

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

        return $next($request);
    }
}