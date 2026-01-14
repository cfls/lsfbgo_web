<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class DeepLinkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Registrar rutas para deep links
        Route::middleware('web')->group(function () {

            // Ruta para manejar deep links del syllabus
            Route::get('/deeplink/syllabus/{path?}', function (Request $request, $path = null) {
                $fullPath = $path ?? '';

                // Construir la URL completa del syllabus
                $syllabusUrl = 'https://lsfbgo.cfls.be/syllabus';
                if ($fullPath) {
                    $syllabusUrl .= '/' . $fullPath;
                }

                // Guardar en sesión para que el scanner lo abra
                session()->put('deeplink_syllabus_url', $syllabusUrl);

                // Redirigir al scanner
                return redirect()->route('scanner');
            })->where('path', '.*')->name('deeplink.syllabus');

            // Ruta para abrir el scanner directamente
            Route::get('/deeplink/scanner', function () {
                return redirect()->route('scanner');
            })->name('deeplink.scanner');

            // Ruta de prueba para desarrollo
            Route::get('/test-deeplink/{path?}', function ($path = null) {
                $url = 'https://lsfbgo.cfls.be/syllabus';
                if ($path) {
                    $url .= '/' . $path;
                }

                session()->put('deeplink_syllabus_url', $url);

                return redirect()->route('scanner');
            })->where('path', '.*');
        });
    }
}