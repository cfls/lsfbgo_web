<?php

use App\Http\Controllers\BackController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\SyllabusGameController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Logout;
use App\Livewire\Auth\ProfileUser;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Dictionary;
use App\Livewire\Dragdrop;
use App\Livewire\Numbers;
use App\Livewire\Options;
use App\Livewire\Practice;
use App\Livewire\Scanner;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Spelling;
use App\Livewire\Syllabus;
use App\Livewire\SyllabusGames;
use App\Livewire\TableuBord;
use App\Livewire\Theme;
use App\Livewire\Themes;
use Illuminate\Support\Facades\Route;
use Native\Mobile\Edge\Edge;
use Native\Mobile\Facades\SecureStorage;


/*
|--------------------------------------------------------------------------
| Rutas de Red (Sin middleware de verificación de red)
|--------------------------------------------------------------------------
| Estas rutas NO deben verificar la conexión para evitar loops infinitos
*/

Route::get('/sin-conexion', [NetworkController::class, 'sinConexion'])
    ->name('sin-conexion')
    ->withoutMiddleware(['check.network']);

Route::get('/api/network/status', [NetworkController::class, 'checkStatus'])
    ->name('api.network.status')
    ->withoutMiddleware(['check.network']);

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Limpiar EDGE en la página de bienvenida/login
    Edge::clear();

    $storedData = SecureStorage::get('data');
    $data = json_decode($storedData, true);

    // Verificar si existe el token en la sesión
    if (!empty($data['token'])) {
        return redirect()->route('access.dashboard');
    }

    return view('welcome');
})->name('home');



Route::get('/verify-code', VerifyEmail::class)->name('verify.email');
Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('access.login');
    Route::get('register', Register::class)->name('access.register');
    Route::get('forgot-password', ForgotPassword::class)->name('pass.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('pass.reset');

});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (requieren token Y conexión de red)
|--------------------------------------------------------------------------
| Estas rutas verificarán automáticamente la conexión de red gracias al
| middleware global CheckNetworkConnection
*/
Route::middleware('api.token.exists')->group(function () {



Route::get('table-au-de-bord', TableuBord::class)->name('access.dashboard');


Route::get('/dictionary', Dictionary::class)->name('dictionary');
Route::get('/practice', Practice::class)->name('practice');
Route::get('/numbers-practice', Numbers::class)->name('numbers.practice');
Route::get('/alphabet-practice', Spelling::class)->name('alphabet.practice');

// ============================================
// REDIRECCIONES DE URLs ANTIGUAS WIX
// ============================================

// Caso 1a: ue1-themes (sin número)
    Route::get('ue1-themes', fn() => redirect()->route('syllabus', ['ue' => 'ue1-themes']));

// Caso 1b: ue1-themes-{número} (libros antiguos con numeración específica)
    Route::get('ue1-themes-1', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'je-me-presente']));
    Route::get('ue1-themes-3', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'ma-famille']));
    Route::get('ue1-themes-4', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'jhabite']));
    Route::get('ue1-themes-5', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'je-me-deplace']));
    Route::get('ue1-themes-6', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'quel-jour-sommes-nous']));
    Route::get('ue1-themes-7', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'ma-routine']));
    Route::get('ue1-themes-8', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'quel-temps-fait-il']));
    Route::get('ue1-themes-9', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'chez-le-medecin']));
    Route::get('ue1-themes-10', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'je-decouvre-mes-sentiments']));
    Route::get('ue1-themes-11', fn() => redirect()->route('syllabus.themes', ['ue' => 'ue1-themes', 'theme' => 'au-restaurant']));

// Caso 2a y 2b: Rutas dinámicas para ue2, ue3, etc. (libros nuevos)
    Route::get('ue{ue}-themes/{theme}', function ($ue, $theme) {
        return redirect()->route('syllabus.themes', [
            'ue' => "ue{$ue}-themes",
            'theme' => $theme
        ]);
    })->where(['ue' => '[0-9]+']);

    Route::get('ue{ue}-themes', function ($ue) {
        return redirect()->route('syllabus', ['ue' => "ue{$ue}-themes"]);
    })->where('ue', '[0-9]+');

// ============================================
// RUTAS PRINCIPALES DE SYLLABUS
// ============================================

Route::get('/syllabus/{ue?}', Syllabus::class)->name('syllabus');
Route::get('/syllabus/{ue}/{theme}', Themes::class)->name('syllabus.themes');
Route::get('/syllabus/{ue}/{theme}/{id}', Theme::class)->name('syllabus.theme');
Route::get('/syllabus-games/{ue?}', SyllabusGames::class)->name('games');
Route::get('/syllabus-games/{ue}/{type?}', Options::class)->name('questions');

Route::get('/syllabus-games/{ue}/{type}/{theme}', [SyllabusGameController::class, 'index'])
    ->name('syllabus.play');


Route::get('/dragdrop', DragDrop::class)->name('games.dragdrop');

Route::get('/scanner', Scanner::class)->name('scanner');

Route::get('settings/profile', ProfileUser::class)->name('profile.edit');
Route::get('/settings/parameters', Profile::class)->name('profile.parameters');
Route::get('settings/password', Password::class)->name('user-password.edit');
Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::match(['get', 'post'], '/logout', [Logout::class, '__invoke'])
        ->name('access.logout');
});
