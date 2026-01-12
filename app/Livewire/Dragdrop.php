<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Dragdrop extends Component
{
    public $title = 'Jeu de lettres - Glisser-déposer';
    public $words = [];
    public $letters = [];
    public $currentWord = null;
    public $slots = [];
    public $completed = false;
    public $slug;
    public $type;
    public $theme;
    public $refreshKey = 0;
    public int $completedGames = 0;   // Cuántas partidas ha completado
    public int $totalScore = 0;       // Puntos acumulados


    // 🔒 Nuevo control de suscripción y demo
    public bool $isComplete = false;
    public bool $hasSubscription = false;
    public bool $demoPlayed = false;
    public bool $isPlaying = false;

    public function mount()
    {

//        logger('TOKEN EN PRODUCCIÓN:', [session('data.token')]);
        $this->completedGames = 1;


        // 2️⃣ Cargar palabras
        $response = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken(session('data.token'))
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/spell/');

        $wordsData = $response->json('data', $response->json());
        $this->words = collect($wordsData)->map(fn($w) => $w['attributes'] ?? $w)->toArray();


        // 3️⃣ Cargar letras
        $responseLetters = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken(session('data.token'))
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/letters/');

        $lettersData = $responseLetters->json('data', $responseLetters->json());
        $this->letters = collect($lettersData)->map(function ($l) {
            $item = $l['attributes'] ?? $l;

            // Si es string, conviértelo en array consistente
            if (is_string($item)) {
                $item = [
                    'symbol' => $item,
                    'image'  => asset('img/default-letter.png'),
                ];
            }

            return $item;
        })->toArray();

        $this->loadRandomWord();
    }

    public function loadRandomWord()
    {
        $this->completed = false;

        if (empty($this->words)) {
            logger('❌ No words available in API');
            return;
        }

        $random = $this->words[array_rand($this->words)];

        $this->currentWord = $random['attributes'] ?? $random;

        $wordName = $this->currentWord['name'] ?? '';
        if ($wordName === '') {
            $this->slots = [];
            return;
        }

        $normalized = $this->normalize($wordName);
        $this->slots = array_fill(0, strlen($normalized), null);

        foreach ($this->letters as &$l) {
            $l['used'] = false;
        }
    }

    public function dropLetter($slotIndex, $symbol)
    {
        if (!$this->currentWord || !isset($this->currentWord['name'])) {
            return;
        }

        $wordName = $this->normalize($this->currentWord['name']);
        if (!isset($wordName[$slotIndex])) {
            return;
        }

        $expected = strtolower($wordName[$slotIndex]);
        $symbol = strtolower($symbol);
        $correct = $symbol === $expected;

        $expectedCount = substr_count($wordName, $symbol);
        $usedCount = collect($this->slots)
            ->where('symbol', $symbol)
            ->where('correct', true)
            ->count();

        if ($usedCount >= $expectedCount) {
            $this->dispatch('shake-slot', slot: $slotIndex);
            return;
        }

        if ($correct) {
            $letter = collect($this->letters)->first(fn($l) => strtolower($l['symbol']) === $symbol);

            $this->slots[$slotIndex] = [
                'symbol' => $symbol,
                'image' => $letter['image'] ?? asset('img/default-letter.png'),
                'correct' => true,
            ];
            $this->dispatch('$refresh');

            $usedCount++;
            foreach ($this->letters as &$l) {
                if (strtolower($l['symbol']) === $symbol) {
                    $l['used'] = $usedCount >= $expectedCount;
                    break;
                }
            }

            $this->checkComplete();
        } else {
            $this->slots[$slotIndex] = ['symbol' => $symbol, 'correct' => false];
            $this->dispatch('shake-slot', slot: $slotIndex);
        }
    }

    public function checkComplete()
    {
        if ($this->completed) {
            return;
        }

        usleep(150000);
        $allCorrect = collect($this->slots)->every(fn($s) => $s && ($s['correct'] ?? false));

        if ($allCorrect) {
            $this->completed = true;

            // Si es demo, marcar como jugado
            if (!$this->hasSubscription) {
                $this->demoPlayed = true;
            }

            $this->dispatch('word-completed', word: $this->currentWord['name']);
            $this->completed();
        }
    }

    public function nextWord()
    {
        // 🚫 Si ya jugó 5 partidas → mostrar mensaje y no cargar más
        if ($this->completedGames >= 5) {
            $this->dispatch('go-next-theme');
            return;
        }

        // Si es demo y ya jugó una, mostrar modal
        if (!$this->hasSubscription && $this->demoPlayed) {
            $this->dispatch('demo-ended');
            return;
        }

        $this->loadRandomWord();
        $this->refreshKey++;
    }

    private function normalize(string $text): string
    {
        $text = strtolower(trim($text));
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return preg_replace('/[^a-z0-9]/', '', $text);
    }

    public function completed(): void
    {
        // 🧮 Sumar puntaje de esta partida
        $this->score = 100;
        $this->totalScore += $this->score;
        $this->completedGames++;

        \Log::info("Juego completado #{$this->completedGames} con total {$this->totalScore} puntos.");

        // 🎯 Si aún no llega a 5 partidas → seguir jugando sin guardar
        if ($this->completedGames < 5) {
            $this->loadRandomWord();   // Genera nueva palabra aleatoria
            $this->refreshKey++;
            return;
        }

        // 🚀 Si ya jugó 5 partidas → marcar como completo y guardar en BD
        $this->isComplete = true;

        $token = session('data.token');
        $userId = session('data.user.id');

        if (!$token || !$userId) {
            logger()->warning('⚠️ No hay sesión válida para guardar el resultado.');
            return;
        }

        // Verificar si ya existe un registro de ese quiz
        $checkUrl = sprintf(
            '%s/v1/quiz-results/check/%s/%s/%s/%s',
            config('services.api.url'),
            session('data.user.id'),
            $this->slug,
            $this->theme,
            $this->type
        );

        $checkResponse = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken($token)
            ->acceptJson()
            ->get($checkUrl);



        if ($checkResponse->successful()) {
            $data = $checkResponse->json('data', []);

            if (!empty($data)) {

                // ✅ Ya existe → no guardar otra vez
                $this->dispatch('game-completed');
                return;
            }
        }

        try {
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($token)
                ->acceptJson()
                ->post(config('services.api.url') . '/v1/quiz-results', [
                    'user_id'   => $userId,
                    'syllabus'  => $this->slug,
                    'theme'     => $this->theme,
                    'type'      => $this->type,
                    'score'     => $this->totalScore,
                    'played_at' => now()->toDateTimeString(),
                ]);

            if ($response->failed()) {
                logger()->error('⚠️ Error al guardar resultado: ' . $response->body());
            } else {
                logger()->info("🎉 Resultado final guardado correctamente ({$this->totalScore} puntos).");
                $this->dispatch('game-completed');
            }
        } catch (\Throwable $e) {
            logger()->error('Error en completed(): ' . $e->getMessage());
        }
    }



        public function render()
    {
        return view('livewire.dragdrop')->layout('components.layouts.app.home', [
            'title' => 'Jeu de lettres - Drag and Drop',
        ]);
    }
}
