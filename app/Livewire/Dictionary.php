<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Dictionary extends Component
{
    public array $items = [];
    public string $title = 'Dictionnaire LSFB';
    /** Filtros */
    public string $search = '';
    public string $letter = 'tous';

    /** UI */
    public bool $isLoading = false;

    public ?array $selectedItem = null;
    public bool $showModal = false;

    public function mount(): void
    {
        $this->fetch();
    }

    /** Cuando cambia la búsqueda */
    public function updatedSearch(): void
    {
        $this->fetch();
    }

    /** Cuando cambia la letra (tabs) */
    public function updatedLetter(): void
    {
        $this->fetch();
    }

    /** Abrir video */
    public function openVideo(int $id): void
    {
        if ($this->selectedItem && $this->selectedItem['id'] === $id) {
            $this->showModal = true;
            return;
        }

        $response = $this->http()->get("/v1/dictionnaire/{$id}");
        $data = $response->json('data')[0] ?? null;

        if (!$data) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Vidéo introuvable.'
            ]);
            return;
        }

        $this->selectedItem = [
            'id' => $data['id'],
            'title' => $data['title'],
            'url' => $data['url'],
        ];

        $this->showModal = true;
    }

    protected function fetch(): void
    {
        $this->isLoading = true;

        $params = [
            'per_page' => 5000, // Cargar todo
        ];

        if ($this->letter !== 'tous') {
            $params['letter'] = strtoupper($this->letter);
        }

        if (trim($this->search) !== '') {
            $params['search'] = $this->search;
        }

        $response = $this->http()->get('/v1/dictionnaire', $params);

        if ($response->successful()) {
            $json = $response->json();
            $data = $json['data'] ?? [];

            $this->items = array_map(function ($item) {
                return [
                    'id'    => $item['id'],
                    'title' => $item['title'] ?? '',
                ];
            }, $data);

            if (empty($this->items)) {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'message' => 'Aucun mot trouvé.',
                ]);
            }
        } else {
            $this->items = [];

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur de chargement.',
            ]);
        }

        $this->isLoading = false;
    }

    /** Cliente HTTP */
    protected function http()
    {
        $baseUrl = rtrim((string) config('services.api.url', url('/api')), '/');
        $token = config('services.api.token') ?? config('services.api.key') ?? env('API_TOKEN');

        $client = Http::baseUrl($baseUrl)->acceptJson()->asJson();

        if ($token) {
            $client = $client->withToken($token);
        }

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    /** Para los tabs de letras */
    public function setLetter($ltr): void
    {
        $this->letter = $ltr;
        $this->fetch();
    }

    public function render()
    {
        $featuredDemos = [ ];
        return view('livewire.dictionary', compact('featuredDemos'))
            ->layout('components.layouts.app.home', [
                'title' => 'Dictionnaire LSFB',
            ]);
    }
}
