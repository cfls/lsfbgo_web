<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Spelling extends Component
{
    public array  $words      = [];
    public array  $letters    = [];
    public int    $index      = 0;
    public string $answer     = '';
    public ?bool  $isCorrect  = null;
    public int $incorrect = 0;

    public int    $score      = 0;
    public int    $roundTotal = 10;
    public int    $total      = 10;
    public bool   $finished   = false;

    public string $difficulty = 'easy';
    public string $title      = 'Exercices épellation';

    public array $bag   = [];
    public array $queue = [];

    public function mount(ApiService $api): void
    {
        $token = session('token');

        if (!$token) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $this->loadWords($api, $token);
    }

    protected function loadWords(ApiService $api, string $token): void
    {

        $allWords = Cache::remember('spellings_words_v2_' . md5($token), 600, function () use ($api, $token) {
            $response = $api->getSpellings($token);

            return $response->ok()
                ? collect($response->json('data', []))
                    ->pluck('attributes')
                    ->where('active', 1)
                    ->map(fn($item) => [
                        'word'       => $item['word'],
                        'difficulty' => $item['difficulty'] ?? 'easy',
                    ])
                    ->filter(fn($item) => !empty($item['word']))
                    ->values()
                    ->toArray()
                : [];
        });

        $this->words = collect($allWords)
            ->where('difficulty', $this->difficulty)
            ->pluck('word')
            ->shuffle()
            ->values()
            ->toArray();

        if (empty($this->words)) {
            $this->words = collect($allWords)
                ->pluck('word')
                ->shuffle()
                ->values()
                ->toArray();
        }

        if (empty($this->words)) {
            $this->finished = true;
            return;
        }

        $this->finished  = false;
        $this->score     = 0;
        $this->answer    = '';
        $this->isCorrect = null;
        $this->total     = $this->roundTotal;
        $this->incorrect = 0;

        $this->buildQueue();
        $this->chooseNextIndex();
        $this->recomputeLetters();
    }

    public function updatedDifficulty(): void
    {
        $api   = app(ApiService::class);
        $token = session('token');
        $this->loadWords($api, $token);
    }

    public function getCurrentWordProperty(): ?string
    {
        if ($this->finished) return null;
        return $this->words[$this->index] ?? null;
    }

    public function checkAnswer(): void
    {
        if (!$this->currentWord) return;

        $expected = $this->normalize($this->currentWord);
        $given    = $this->normalize($this->answer);

        $this->isCorrect = ($expected === $given);

        if ($this->isCorrect) {
            $this->score++;
        } else {
            $this->incorrect++; // ← nuevo
        }
    }

    public function next(): void
    {
        if ($this->isCorrect === null) {
            $this->checkAnswer();
        }

        if ($this->isCorrect !== true) {
            return;
        }

        if ($this->score >= $this->roundTotal) {
            $this->finished  = true;
            $this->answer    = '';
            $this->isCorrect = null;
            $this->letters   = [];
            return;
        }

        $this->chooseNextIndex(); // ← sin parámetro, la cola maneja todo
        $this->answer    = '';
        $this->isCorrect = null;
        $this->recomputeLetters();
    }

    public function restart(): void
    {
        $this->incorrect = 0;
        $this->score     = 0;
        $this->finished  = false;
        $this->answer    = '';
        $this->isCorrect = null;

        $this->buildQueue();
        $this->chooseNextIndex();
        $this->recomputeLetters();
    }

    // Ya no se usa pero se mantiene por compatibilidad
    protected function refillBag(): void
    {
        $this->bag = [];
    }

    /**
     * Genera una secuencia de roundTotal palabras únicas y sin repetición
     * para el turno completo.
     */
    protected function buildQueue(): void
    {
        $indices = array_keys($this->words);
        $total   = count($indices);

        if ($total === 0) return;

        // Si hay suficientes palabras, simplemente mezclar y tomar 10
        if ($total >= $this->roundTotal) {
            shuffle($indices);
            $this->queue = array_slice($indices, 0, $this->roundTotal);
            return;
        }

        // Si hay menos de 10 palabras → rellenar hasta 10 evitando repetir consecutivos
        $queue    = [];
        $lastUsed = null;

        while (count($queue) < $this->roundTotal) {
            // Candidatos: todos excepto el último usado
            $candidates = $lastUsed !== null
                ? array_values(array_diff($indices, [$lastUsed]))
                : $indices;

            // Si solo hay 1 palabra no hay opción, se repite igual
            if (empty($candidates)) {
                $candidates = $indices;
            }

            $pick     = $candidates[array_rand($candidates)];
            $queue[]  = $pick;
            $lastUsed = $pick;
        }

        $this->queue = $queue;
    }

    protected function chooseNextIndex(?int $avoid = null): void
    {
        if (!empty($this->queue)) {
            $next        = array_shift($this->queue);
            $this->index = (int) $next;
        } else {
            // Cola vacía → terminar el turno
            $this->finished  = true;
            $this->answer    = '';
            $this->isCorrect = null;
            $this->letters   = [];
        }
    }

    protected function recomputeLetters(): void
    {
        if (!$this->currentWord) {
            $this->letters = [];
            return;
        }

        $normalized    = $this->normalize($this->currentWord);
        $this->letters = preg_split('//u', (string) $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));

        $text = str_replace(
            ['é','è','ê','ë','à','â','ä','î','ï','ô','ö','ù','û','ü','ç','æ','œ','É','È','Ê','Ë','À','Â','Î','Ï','Ô','Ù','Û','Ü','Ç'],
            ['e','e','e','e','a','a','a','i','i','o','o','u','u','u','c','ae','oe','e','e','e','e','a','a','i','i','o','u','u','u','c'],
            $text
        );

        return preg_replace('/[^a-z0-9]/', '', $text);
    }

    public function retryNext(): void
    {
        $this->answer    = '';
        $this->isCorrect = null;

        $this->chooseNextIndex();
        $this->recomputeLetters();
    }

    public function render()
    {
        return view('livewire.spelling')->layout('components.layouts.app.home', [
            'title' => 'Exercices épellation',
        ]);
    }
}