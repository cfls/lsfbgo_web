<?php

namespace App\Livewire\Settings;

use App\Services\ApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Password extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(ApiService $api): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        $response = $api->post('/auth/password', [
            'current_password' => $validated['current_password'],
            'password' => $validated['password'],
            'password_confirmation' => $this->password_confirmation,
        ]);

        if ($response->failed()) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw ValidationException::withMessages([
                'current_password' => $response->json('message') ?? 'Le mot de passe actuel est incorrect.',
            ]);
        }

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }

    public function render()
    {
        return view('livewire.settings.password')->layout('components.layouts.app.home');
    }
}
