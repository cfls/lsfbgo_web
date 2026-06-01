<?php

namespace App\Livewire\Settings;

use App\Livewire\Actions\Logout;
use App\Services\ApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\Attributes\Layout;

class DeleteUserForm extends Component
{
    public string $password = '';
    public $storedData;

    /**
     * Delete the currently authenticated user.
     */
    #[Layout('components.layouts.app')]
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
        return view('livewire.settings.delete-user-form');
    }

}
