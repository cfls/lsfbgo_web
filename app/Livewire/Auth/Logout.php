<?php

namespace App\Livewire\Auth;

use App\Services\ApiService;
use Illuminate\Support\Facades\Session;
use Native\Mobile\Facades\SecureStorage;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        try {
            $apiService = app(ApiService::class);

            // Intentar hacer logout en la API
            try {
                $response = $apiService->logout();

                \Log::info('Logout API response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            } catch (\Exception $e) {
                // Si falla el logout en la API, continuar con la limpieza local
                \Log::warning('Error al hacer logout en API', [
                    'error' => $e->getMessage()
                ]);
            }

            // Limpiar almacenamiento local (siempre se ejecuta)
            SecureStorage::delete('data');
            SecureStorage::delete('token');

            // Limpiar sesión
            Session::flush();
            Session::regenerate();

            return redirect()->route('home')->with('success', 'Déconnexion réussie');

        } catch (\Exception $e) {
            \Log::error('Error durante logout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Aún así, intentar limpiar localmente
            try {
                SecureStorage::delete('data');
                SecureStorage::delete('token');
                Session::flush();
                Session::regenerate();
            } catch (\Exception $cleanupError) {
                \Log::error('Error al limpiar datos locales', [
                    'error' => $cleanupError->getMessage()
                ]);
            }

            return redirect()->route('home')->with('error', 'Erreur lors de la déconnexion');
        }
    }
}