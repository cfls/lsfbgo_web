<?php

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



Route::get('/', function () {
    // Verificar si existe el token en la sesión
    if (session()->has('data') && !empty(session('data.token'))) {
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

// Rutas protegidas (requieren token)
Route::middleware('api.token.exists')->group(function () {



Route::get('table-au-de-bord', TableuBord::class)->name('access.dashboard');


Route::get('/dictionary', Dictionary::class)->name('dictionary');
Route::get('/practice', Practice::class)->name('practice');
Route::get('/numbers-practice', Numbers::class)->name('numbers.practice');
Route::get('/alphabet-practice', Spelling::class)->name('alphabet.practice');
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
