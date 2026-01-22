<?php

namespace App\Livewire\Auth;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Mobile\Edge\Edge;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public $code;

    public ?string $error = null;

    public function mount(): void
    {
          Edge::clear();

        // Si ya hay sesión activa, redirigir
        if (session()->has('data')) {
            $this->redirect(route('access.dashboard'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }

    public function login(ApiService $api): void
    {
        $this->validate();
        $this->error = null;

        $response = $api->login($this->email, $this->password);

        if ($response->successful()) {
            $data = data_get($response->json(), 'data');

            if (!$data) {
                \Log::error('Login API did not return data', ['response' => $response->json()]);
                $this->error = 'No se pudo obtener el token del servidor.';
                return;
            }

            session(['data' => $data]);

            $this->redirect(route('access.dashboard'), navigate: true);
            return;
        }



        $this->error = $response->json('message');
    }


    public function sendCode(ApiService $api)
    {

           $this->validate();


           $api->sendVerificationCode($this->email);
        // Enviar el correo con el código de verificación

          return redirect()->route('verify.email');
    }
}