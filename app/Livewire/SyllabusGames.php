<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use function Laravel\Prompts\progress;

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
    public array $quizCounts = [];
    public int $requiredCount = 10;
    private array $syllabusThemesCache = [];
    public bool $syllabusCompleted = false;

    public function mount(?string $ue = null): void
    {
        $this->ue = $ue;

        $token = session('token');
        if (!$token) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $initialTheme = $this->ue ?: 'ue1-themes';
        $this->selectedSyllabus = $initialTheme;

        $this->allThemes(); // ← MOVER ACÁ, antes de loadQuizCounts()

        $this->loadQuizCounts(); // ahora $this->themes ya está poblado
        $this->finishQuiz();

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

        $this->loadTheme($initialTheme);
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

    private function loadQuizCounts(): void
    {
        $types = ['text', 'choice', 'yes-no', 'video-choice', 'match'];
        $initialTheme = $this->selectedSyllabus ?: 'ue1-themes';

        $api = app(ApiService::class);

        foreach ($types as $type) {
            $response = $api->GetResultQuizForTopic(
                session('data.user.id'),
                $initialTheme,
                $type
            );

            $data = $response->json('data')['count'] ?? 0;
            $this->quizCounts[$type] = is_numeric($data) ? (int) $data : 0;
        }

        $this->requiredCount = $this->calculateRequiredCount($initialTheme);
    }

    private function calculateRequiredCount(string $ue): int
    {
        // Traer los TEMAS individuales de este syllabus específico (no el dropdown de syllabus)
        $themesResponse = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
            'timeout' => 30,
            'connect_timeout' => 10,
        ])
            ->withToken(session('token'))
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/themes/' . $ue);

        $themes = collect($themesResponse->json('data', []));

        $count = $themes->count()
            + $themes->where('attributes.type', 'pour_en_savoir_plus')->count();

        return $count > 0 ? $count : 10; // fallback conservador
    }

    public function finishQuiz(): void
    {
        if (!$this->selectedSyllabus) {
            $this->selectedSyllabus = $this->ue ?: 'ue1-themes';
        }

        $api = app(ApiService::class);
        $response = $api->FinishSyllabus(session('data.user.id'), $this->selectedSyllabus);

        $data = $response->json('data');



        $totalCompleted = $data['progress'][$this->selectedSyllabus]['total_completed'] ?? 0;

        $this->syllabusCompleted = $totalCompleted > 0;
    }

    public function updatedSelectedSyllabus($value): void
    {
        $this->loadQuizCounts();
        $this->finishQuiz();
        $this->loadTheme($value);
    }

    public function isUnlocked(): bool
    {
        $types = ['text', 'choice', 'yes-no', 'video-choice', 'match'];
        foreach ($types as $type) {
            if (($this->quizCounts[$type] ?? 0) < $this->requiredCount) return false;
        }
        return true;
    }

    public function render()
    {
        return view('livewire.syllabus-games')->layout('components.layouts.app.home');
    }
}