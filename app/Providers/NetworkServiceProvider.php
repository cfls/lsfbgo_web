<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Native\Mobile\Facades\Network;

class NetworkServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar el singleton para el estado de la red
        $this->app->singleton('network.status', function ($app) {
            try {
                return Network::status();
            } catch (\Exception $e) {
                \Log::warning('No se pudo obtener el estado de red: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Compartir estado de red con TODAS las vistas
        |--------------------------------------------------------------------------
        | Esto hace que las variables $networkConnected, $networkType y 
        | $networkStatus estén disponibles en TODAS tus vistas Blade
        */
        View::composer('*', function ($view) {
            try {
                $networkStatus = Network::status();
                
                $view->with([
                    'networkConnected' => $networkStatus ? $networkStatus->connected : true,
                    'networkType' => $networkStatus ? $networkStatus->type : 'unknown',
                    'networkStatus' => $networkStatus,
                ]);
            } catch (\Exception $e) {
                // Si hay error, asumir que hay conexión para no bloquear la app
                $view->with([
                    'networkConnected' => true,
                    'networkType' => 'unknown',
                    'networkStatus' => null,
                ]);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Directivas Blade personalizadas
        |--------------------------------------------------------------------------
        | Puedes usar estas directivas en tus vistas:
        | @connected ... @endconnected
        | @disconnected ... @enddisconnected
        | @networkType('wifi') ... @endnetworkType
        */
        
        // Directiva para verificar si hay conexión
        Blade::if('connected', function () {
            try {
                $networkStatus = Network::status();
                return $networkStatus && $networkStatus->connected;
            } catch (\Exception $e) {
                return true;
            }
        });

        // Directiva para verificar si NO hay conexión
        Blade::if('disconnected', function () {
            try {
                $networkStatus = Network::status();
                return !$networkStatus || !$networkStatus->connected;
            } catch (\Exception $e) {
                return false;
            }
        });

        // Directiva para verificar el tipo de red
        Blade::if('networkType', function ($type) {
            try {
                $networkStatus = Network::status();
                return $networkStatus && $networkStatus->type === $type;
            } catch (\Exception $e) {
                return false;
            }
        });

        // Directiva para verificar si la conexión es cara (iOS)
        Blade::if('expensiveNetwork', function () {
            try {
                $networkStatus = Network::status();
                return $networkStatus && ($networkStatus->isExpensive ?? false);
            } catch (\Exception $e) {
                return false;
            }
        });
    }
}
