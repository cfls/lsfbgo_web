<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Native\Mobile\Facades\SecureStorage;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (SecureStorage::get('data')) {
            return redirect()->route('access.dashboard');
        }

        return $next($request);
    }
}