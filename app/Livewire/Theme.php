<?php

namespace App\Livewire;

use App\Services\ApiService;
use Livewire\Component;

class Theme extends Component
{
    public array $results = [];
    public array $videos = [];
    public array $principalVideos = [];
    public array $pourEnSavoirPlusVideos = [];
    public int $currentIndex = 0;
    public string $theme = '';
    public string $ue = '';
    public int $id = 0;
    public string $section = 'principal';

    public function mount(string $theme, string $ue, int $id): void
    {
        $this->theme = $theme;
        $this->ue = $ue;
        $this->id = $id;

        $api = app(ApiService::class);
        $response = $api->ThemeSign($ue, $theme);

        if ($response->successful()) {
            $this->results = $response->json('data', []);

            $videos = collect($this->results['attributes']['videos'] ?? [])
                ->map(fn ($video) => $this->normalizeVideo($video))
                ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            $annexes = collect($this->results['attributes']['annexes'] ?? [])
                ->map(fn ($video) => $this->normalizeVideo($video))
                ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            $this->principalVideos = $videos->all();
            $this->pourEnSavoirPlusVideos = $annexes->all();

            $selectedInPrincipal = $videos->firstWhere('id', $id);
            $selectedInPlus = $annexes->firstWhere('id', $id);

            if ($selectedInPlus) {
                $this->section = 'plus';
                $this->videos = $this->pourEnSavoirPlusVideos;
            } else {
                $this->section = 'principal';
                $this->videos = $this->principalVideos;
            }

            $foundIndex = collect($this->videos)->search(
                fn ($video) => (int) ($video['id'] ?? 0) === (int) $id
            );

            $this->currentIndex = $foundIndex !== false ? $foundIndex : 0;
        }
    }

    private function normalizeVideo(array $video): array
    {
        if (isset($video['url'])) {
            $video['url'] = str_replace('http://', 'https://', $video['url']);
        }

        return $video;
    }

    public function render()
    {
        return view('livewire.theme')->layout('components.layouts.app.home', [
            'title' => 'Theme',
        ]);
    }
}