<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Options extends Component
{
    public $results = [];
    public $themes = [];
    public $doneThemesData = [];
    public $type;
    public ?string $ue = null;
    public $syllabusData;
    public $doneThemes = [];

    public function mount(?string $ue = null)
    {
        $this->ue = $ue;

        $token = session('token');
        $data = session('data');

        if (!$token || !$data) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $syllabusResponse = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
            'timeout' => 30,
            'connect_timeout' => 10,
        ])
            ->withToken($token)
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/syllabus/settings/' . $this->ue);

        $this->syllabusData = $syllabusResponse->json('data', []);

        if ($this->type !== 'tous') {
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
                'timeout' => 30,
                'connect_timeout' => 10,
            ])
                ->withToken($token)
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/themes/' . $this->ue);

            $this->themes = $response->json('data', []);

            $doneThemesResponse = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
                'timeout' => 30,
                'connect_timeout' => 10,
            ])
                ->withToken($token)
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/quiz-results/' . $data['user']['id']);

            $this->doneThemesData = $doneThemesResponse->json('data', []);

            $this->doneThemes = collect($this->doneThemesData)
                ->map(fn ($item) => [
                    'syllabus' => $item['syllabus'] ?? null,
                    'theme' => $item['theme'] ?? null,
                    'type' => $item['type'] ?? null,
                ])
                ->toArray();
        }
    }

    public function render()
    {
        if ($this->type == 'tous') {
            return view('syllabus.theme_nevel')->layout('components.layouts.app.home', [
                'title' => 'Questions Options',
            ]);
        }

        return view('livewire.options')->layout('components.layouts.app.home', [
            'title' => 'Questions Options',
        ]);
    }
}