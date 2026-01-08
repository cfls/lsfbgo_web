<?php

namespace App\Livewire;

use App\Services\ApiService;
use Livewire\Component;

class Themes extends Component
{
    public $results = [];

    public function mount(ApiService $api, string $ue, string $theme){

        $response = $api->ThemeSyllabus($ue, $theme);



        $this->results = $response->json('data', []);
    }

    public function render()
    {
        return view('livewire.themes')->layout('components.layouts.app.home', [
            'title' => 'Theme'
        ]);
    }
}
