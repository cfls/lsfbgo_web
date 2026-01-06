<?php

namespace App\Livewire\Auth;

use App\Services\ApiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{

    #[Validate('required|string|email')]
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPassword(ApiService $api): void
    {


        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);



        $response = $api->sendPasswordResetLink($this->email);

        if ($response->successful()) {
            session()->flash('status', __('Un lien de réinitialisation sera envoyé si le compte existe.'));
        } else {
            $this->addError('email', $response->json('message') ?? 'Impossible d’envoyer le lien de réinitialisation.');
        }
    }
}
