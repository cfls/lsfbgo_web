<?php

namespace App\Livewire\Auth;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class VerifyEmail extends Component
{
    public string $email = '';

    #[Validate('required|string|size:6')]
    public string $code = '';

    public ?string $message = null;
    public ?string $error = null;

    public function mount()
    {
        $this->email = session('pending_email', '');
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }

    public function verify(ApiService $api): void
    {

        $this->validate();

        $response = $api->verifyEmail($this->email, $this->code);



        if ($response->successful()) {
            // Eliminamos el email temporal de la sesión
            session()->forget('pending_email');


            // Mostramos un mensaje y redirigimos al login
            $this->message = $response->json('message') ?? 'Votre compte a été vérifié avec succès.';
            $this->redirect(route('access.login'));
            return;
        }

        $this->error = $response->json('message') ?? 'Le code est invalide ou expiré.';
    }

    public function resendCode()
    {

        $this->error = null;
        $this->message = 'Fonction de renvoi non implémentée pour le moment.';
    }
}
