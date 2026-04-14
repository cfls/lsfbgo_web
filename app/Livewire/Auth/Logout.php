<?php

namespace App\Livewire\Auth;

use App\Services\ApiService;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        try {
            $apiService = app(ApiService::class);

            try {
                $response = $apiService->logout();

                \Log::info('Logout API response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Error al hacer logout en API', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Limpiar sesión web
            Session::forget('data');
            Session::flush();
            Session::invalidate();
            Session::regenerateToken();

            return redirect()->route('access.dashboard')->with('success', 'Déconnexion réussie');

        } catch (\Exception $e) {
            \Log::error('Error durante logout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            try {
                Session::forget('data');
                Session::flush();
                Session::invalidate();
                Session::regenerateToken();
            } catch (\Exception $cleanupError) {
                \Log::error('Error al limpiar datos locales', [
                    'error' => $cleanupError->getMessage(),
                ]);
            }

            return redirect()->route('access.dashboard')->with('error', 'Erreur lors de la déconnexion');
        }
    }
}