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

        $videoId = pathinfo($data['url'], PATHINFO_FILENAME);
        $optimizedUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/q_auto:low,w_400,f_auto/{$videoId}.mp4";
        $posterUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/so_0,w_400,q_auto:low/{$videoId}.jpg";

        $this->video = [
            'id' => $data['id'],
            'title' => $data['title'],
            'url' => $optimizedUrl,
            'poster' => $posterUrl,
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
