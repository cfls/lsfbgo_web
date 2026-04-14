<?php

namespace App\Livewire;

use App\Services\ApiService;
use Livewire\Component;

class Themes extends Component
{
    public $results = [];
    public string $theme;
    public string $ue;
    public string $color = '#000000';

    public function mount(ApiService $api, string $ue, string $theme): void
    {
        $this->ue = $ue;
        $this->theme = $theme;

        $response = $api->ThemeSyllabus($ue, $theme);
        $responseColor = $api->ThemeColor($ue);

        $this->results = $response->json('data', []);
        $this->color = $responseColor->json('data.attributes.hex_color', '#000000');
    }

    public function render()
    {
        $data = session('data');
        $userId = $data['user']['id'] ?? null;

        if (!$userId) {
            return redirect()->route('home');
        }

        $api = app(ApiService::class);
        $verifyCode = $api->verifycodeSyllabu($userId, $this->ue);

        $verifyData = $verifyCode->json('data', []);
        $isActive = !empty($verifyData) && (($verifyData[0]['attributes']['active'] ?? 0) == 1);

        if (!$isActive) {
            session()->flash('error', "Ce contenu nécessite l'achat du livre Syllabus correspondant pour y accéder.");

            return view('livewire.scanner', [
                'data' => '',
                'streaming' => '',
                'scanner' => 0,
            ])->layout('components.layouts.app.home', [
                'title' => 'Scanner',
            ]);
        }

        return view('livewire.themes')->layout('components.layouts.app.home', [
            'title' => 'Theme',
        ]);
    }
}