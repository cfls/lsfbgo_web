<?php

namespace App\Livewire;

use App\Services\ApiService;
use Livewire\Component;

class Theme extends Component
{
    public array $results = [];
    public array $videos = [];
    public int $currentIndex = 0;
    public string $theme = '';
    public string $ue = '';
    public int $id = 0;

    public function mount(string $theme, string $ue, int $id)
    {
        $this->theme = $theme;
        $this->ue = $ue;
        $this->id = $id;

        $api = app(ApiService::class);
        $response = $api->ThemeSign($ue, $theme);

        // Verificar si la respuesta es exitosa
        if ($response->successful()) {
            $this->results = $response->json('data', []);
            $this->videos = $this->results['attributes']['videos'] ?? [];


            // Buscar índice actual
            $foundIndex = collect($this->videos)->search(function ($video) use ($id) {
                return isset($video['id']) && (int)$video['id'] === $id;
            });

            // Si se encuentra, asignar; si no, usar 0
            $this->currentIndex = $foundIndex !== false ? $foundIndex : 0;
        }
    }

    public function render()
    {
        return view('livewire.theme')->layout('components.layouts.app.home', [
            'title' => 'Theme'
        ]);
    }
}
