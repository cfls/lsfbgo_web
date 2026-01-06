<?php

namespace App\Livewire\Auth;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    #[Validate('required|string|min:3')]
    public string $name = '';

    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public ?string $error = null; // Holding an error if it happened while calling an API


    public function render()
    {
        return view('livewire.auth.register');
    }

    public function register(ApiService $api): void
    {
        $this->validate();
        $this->error = null;

        $response = $api->register([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        if ($response->successful()) {
            session()->put('pending_email', $this->email);
            session()->put('token', $response->json('token'));

            $this->redirect(route('verify.email'));
            return;
        }

        $this->error = $response->json('message');
    }
}
