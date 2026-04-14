<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $data = session('data');

        if (!$data) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $this->name = $data['user']['name'] ?? '';
        $this->email = $data['user']['email'] ?? '';
    }

    public function updateProfileInformation(): void
    {
        $data = session('data');

        if (!isset($data['token'])) {
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
            ->withToken($data['token'])
            ->put(config('services.api.url') . '/auth/profile', $validated);

        if ($response->successful()) {
            $userData = $response->json('data');

            session([
                'data' => [
                    'token' => $data['token'],
                    'user'  => $userData,
                ],
            ]);

            $this->name = $userData['name'] ?? $this->name;
            $this->email = $userData['email'] ?? $this->email;

            $this->dispatch('profile-updated', name: $userData['name'] ?? $this->name);
        } else {
            $this->addError(
                'email',
                $response->json('message') ?? 'Error al actualizar el perfil.'
            );
        }
    }

    public function render()
    {
        return view('livewire.settings.profile')
            ->layout('components.layouts.app.home');
    }
}