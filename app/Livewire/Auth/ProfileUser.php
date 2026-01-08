<?php

namespace App\Livewire\Auth;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ProfileUser extends Component
{
    public $profile = [];

    public function mount(ApiService $api) {

        $response = $api->ProfilUser();


        $this->profile = $response->json('data', []);
    }

    public function render()
    {
        return view('livewire.auth.profile')->layout('components.layouts.app.home');
    }
}
