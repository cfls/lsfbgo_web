<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Syllabus extends Component
{
    public string $title = 'Mes Syllabus';
    public int $optionGame = 0;
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
    public $role;
    public string $accessCode = '';
    public string $link = '';
    public string $theme = '';

    public function mount(?string $ue = null)
    {
        $this->ue = $ue;

        if ($this->ue) {
            $this->loadTheme($this->ue);
        } else {
            $this->loadAllThemes();
        }
    }

    public function loadAllThemes()
    {
        $api = app(ApiService::class);
        $dataUser = session('data');
        $user = $dataUser['user'] ?? [];

        $this->role = $user['role'] ?? 'unknown';

        $verifyUser = $api->verifycodeStatus($user['id']);
        $this->verifyUser = collect($verifyUser->json('data', []));

        $activeThemes = $this->verifyUser
            ->where('attributes.active', 1)
            ->pluck('attributes.theme');

        $responseUser = $api->allThemes();
        $allThemes = collect($responseUser->json('data', []));

        $this->results = $allThemes
            ->map(function ($theme) use ($activeThemes) {
                $theme['isActive'] = $activeThemes->contains($theme['attributes']['slug']);
                return $theme;
            })
            ->values()
            ->all();
    }

    public function loadTheme($ue)
    {
        $data = session('data');
        $token = $data['token'] ?? null;

        if (!$token) {
            return;
        }

        $response = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken($token)
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/themes/' . $ue);

        $this->selectedTheme = $response->json('data', []);
    }

    public function openPaymentModal($link, $theme): void
    {
        $this->theme = $theme;
        $this->selectedLink = $link;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->redirectRoute('syllabus');
    }

    public function openShop(): void
    {
        if ($this->selectedLink) {
            $this->dispatch('open-link', url: $this->selectedLink);
        }
    }

    public function validateCode(): void
    {
        $this->validate([
            'accessCode' => ['required', 'string'],
        ]);

        $api = app(ApiService::class);
        $dataUser = session('data');
        $user = $dataUser['user'] ?? [];

        $verifyUser = $api->Code($user['id'], $this->accessCode, $this->theme);

        if ($verifyUser->successful() && $verifyUser->json('data.attributes.active') === 1) {
            $this->showPaymentModal = false;
            $this->accessCode = '';
        } else {
            $this->addError('accessCode', 'Code invalide');
        }
    }

    public function openInApp()
    {
        $this->dispatch('open-link', url: 'https://www.facebook.com/share/v/1BepzAgdKA');
    }

    public function openSystem()
    {
        $this->dispatch('open-link', url: 'https://nativephp.com');
    }

    public function render()
    {
        return view('livewire.syllabus', [
            'selectedTheme' => $this->selectedTheme,
        ])->layout('components.layouts.app.home', [
            'title' => 'Syllabus',
        ]);
    }
}