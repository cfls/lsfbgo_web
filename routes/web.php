<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Dictionary;
use App\Livewire\Numbers;
use App\Livewire\Practice;
use App\Livewire\Scanner;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Syllabus;
use App\Livewire\TableuBord;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;


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

Route::get('table-au-de-bord', TableuBord::class)->name('access.dashboard');


Route::get('/dictionary', Dictionary::class)->name('dictionary');
Route::get('/practice', Practice::class)->name('practice');
Route::get('/numbers-practice', Numbers::class)->name('numbers.practice');
Route::get('/syllabus', Syllabus::class)->name('syllabus');
Route::get('/scanner', Scanner::class)->name('scanner');
;

//Route::view('dashboard', 'dashboard')
//    ->middleware(['auth', 'verified'])
//    ->name('dashboard');

Route::middleware(['api.token.exists'])->group(function () {
    //Route::get('login', Login::class)->name('login');


    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
