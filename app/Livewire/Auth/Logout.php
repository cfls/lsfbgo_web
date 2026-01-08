<?php

namespace App\Livewire\Auth;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        $token = Session::get('data.token');



        if ($token) {
            try {
                Http::withOptions([
                    'verify' => env('API_VERIFY_SSL', true),
                ])
                    ->withToken($token)
                    ->post(config('services.api.url') . '/auth/logout');
            } catch (\Exception $e) {
                // Opcional: loguear el error
                \Log::warning('Error al hacer logout en API: ' . $e->getMessage());
            }
        }

        Session::forget('data');
        Session::invalidate();
        Session::regenerateToken();


        return redirect()->route('home');
    }
}