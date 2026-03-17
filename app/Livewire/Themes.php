<?php

namespace App\Livewire;

use App\Services\ApiService;
use Livewire\Component;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;


class Themes extends Component
{
    public $results = [];
    public string $theme;
    public string $ue;
    public string $color;

    public function mount(ApiService $api, string $ue, string $theme){

            $this->theme = $theme;
            $response = $api->ThemeSyllabus($ue, $theme);
            $responseColor = $api->ThemeColor($ue);
            
            $this->results = $response->json('data', []);
            $this->color = $responseColor->json('data.attributes.hex_color', '#000000');

           


    }

    public function render()
    {
        $storedData = SecureStorage::get('data');
        $data = json_decode($storedData, true);
        $userId = $data['user']['id'] ?? null;

        $api = app(ApiService::class);

        $verifyCode = $api->verifycodeSyllabu($userId,  $this->ue);



        if (empty($verifyCode->json('data')) || $verifyCode['data'][0]['attributes']['active'] == 0) {
            Dialog::alert('Accès Syllabus', 'Ce contenu nécessite l\'achat du livre Syllabus correspondant pour y accéder');
            return view('livewire.scanner', [
                'data' => '',
                'streaming' => '',
                'scanner' => 0
              ])->layout('components.layouts.app.home', [
                    'title' => 'Scanner',

                ]);

        } else {
            return view('livewire.themes')->layout('components.layouts.app.home', [
                'title' => 'Theme'
            ]);
        }
    }
}
