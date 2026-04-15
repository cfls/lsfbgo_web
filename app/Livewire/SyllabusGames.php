<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SyllabusGames extends Component
{
    public string $title = 'Jeux LSFB';
    public int $optionGame = 1;
    public $verifyUser;
    public $selectUE = [];
    public $selectedUE = null;
    public $search = '';
    public $results = [];
    public $selectedTheme = false;
    public $themes = [];
    public $showPaymentModal = false;
    public $selectedSyllabuForPayment = null;
    public $selectedLink = null;
    public ?string $ue = null;
    public $sections = [];
    public $syllabusData;
    public $color = '#000000';
    public $selectedSyllabus = null;

    public function mount(?string $ue = null): void
    {
        $this->ue = $ue;

        $token = session('token');

        if (!$token) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        if ($this->ue) {
            $syllabusResponse = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
                'timeout' => 60,
                'connect_timeout' => 10,
            ])
                ->withToken($token)
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/syllabus/settings/' . $this->ue);

            $this->syllabusData = $syllabusResponse->json('data', []);
        }

        $this->allThemes();
        $this->loadTheme('ue1-themes');
    }

    public function allThemes()
    {
        $api = app(ApiService::class);
        $response = $api->allThemes();

        $this->themes = $response->json('data', []);
    }

    public function loadTheme($ue): void
    {
        $token = session('token');

        if (!$token) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $this->ue = $ue;
        $baseUrl  = config('services.api.url');
        $options  = [
            'verify'          => env('API_VERIFY_SSL', true),
            'timeout'         => 60,
            'connect_timeout' => 10,
        ];

        [$sectionsResponse, $colorResponse] = Http::pool(fn ($pool) => [
            $pool->withOptions($options)
                ->withToken($token)
                ->acceptJson()
                ->get("{$baseUrl}/v1/sections/{$ue}"),

            $pool->withOptions($options)
                ->withToken($token)
                ->acceptJson()
                ->get("{$baseUrl}/v1/syllabus/settings/{$ue}"),
        ]);

        $this->results = $sectionsResponse->json('data', []);

        // ✅ Primera vez llama a la API, las siguientes 5 min usa el caché
        $this->color = cache()->remember(
            "theme_color_{$ue}",
            now()->addMinutes(5),
            fn() => $colorResponse->json('data.attributes.hex_color', '#000000')
        );

        $this->dispatch('color-updated', color: $this->color);
    }

    public function updatedSelectedSyllabus($value): void
    {
        $this->loadTheme($value);
    }

    public function render()
    {
        return view('livewire.syllabus-games')->layout('components.layouts.app.home');
    }
}