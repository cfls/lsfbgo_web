<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $data = session('data');

        if (!$data) {
            $this->redirect(route('home')); // si no hay sesión, fuera
        }

        $this->name = $data['user']['name'] ?? '';
        $this->email = $data['user']['email'] ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $session = session('data');

        if (!isset($session['token'])) {
            $this->addError('email', 'Debe iniciar sesión nuevamente.');
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ]);

        $response = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->asJson()
            ->acceptJson()
            ->withToken($session['token'])
            ->put(config('services.api.url') . '/auth/profile', $validated);

        if ($response->successful()) {
            $userData = $response->json('data');

            // conservar el token
            session([
                'data' => [
                    'token' => $session['token'],
                    'user'  => $userData,
                ],
            ]);

            $this->dispatch('profile-updated', name: $userData['name']);
        } else {
            $this->addError('email', $response->json('message') ?? 'Error al actualizar el perfil.');
        }
    }


    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('access.dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function render() {
        return view('livewire.settings.profile');
    }
}
