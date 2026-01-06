<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class VideoModal extends Component
{
    public bool $open = false;
    public ?array $video = null;

    protected $listeners = ['openVideoModal' => 'open'];

    public function open(int $id): void
    {

        $client = Http::acceptJson();

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        $response = $client->get(config('services.api.url') . "/v1/dictionnaire/{$id}");

        $data = $response->json('data')[0] ?? null;

        if (!$data) {
            $this->dispatch('notify', type: 'error', message: 'Vidéo introuvable');
            return;
        }

        $this->video = [
            'id' => $data['id'],
            'title' => $data['title'],
            'url' => $data['url'],
        ];

        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->video = null;
    }

    public function render()
    {
        return view('livewire.video-modal');
    }
}
