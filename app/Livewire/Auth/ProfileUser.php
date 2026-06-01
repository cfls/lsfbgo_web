<?php

namespace App\Livewire\Auth;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
//use Native\Mobile\Facades\SecureStorage;
use function Pest\Laravel\json;

class ProfileUser extends Component
{
    public $profile = [];
    public $loading = true;
    public $error = null;
    public string $password = '';

    public function mount(ApiService $api)
    {
        try {
            $response = $api->ProfilUser();



            if ($response->successful()) {
                $this->profile = $response->json();
                $this->loading = false;
            } else {
                $this->error = 'Error al cargar el perfil';
                $this->loading = false;
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->loading = false;
        }
    }

    public function deleteUser(ApiService $api, Logout $logout): void
    {
        $profile = $api->ProfilUser();

        if ($profile->failed()) {
            $this->addError('password', 'Utilisateur non trouvé.');
            return;
        }

        $userId = $profile->json('id');

        $response = $api->deleteAccount($userId, $this->password);

        if ($response->failed()) {
            $this->addError(
                'password',
                $response->json('message') ?? 'Erreur lors de la suppression.'
            );

            return;
        }

        session()->forget(['token', 'data']);

        $logout();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.profile')->layout('components.layouts.app.home');
    }
}
