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

    public int    $score      = 0;
    public int    $roundTotal = 10;
    public int    $total      = 10;
    public bool   $finished   = false;

    public string $difficulty = 'easy'; // ✅

    protected array $bag      = [];
    public string   $title    = 'Exercices épellation';

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

        $this->refillBag();
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

        $this->chooseNextIndex($this->index);
        $this->answer    = '';
        $this->isCorrect = null;
        $this->recomputeLetters();
    }

    public function restart(): void
    {
        $this->score     = 0;
        $this->finished  = false;
        $this->answer    = '';
        $this->isCorrect = null;

        $this->refillBag();
        $this->chooseNextIndex();
        $this->recomputeLetters();
    }

    protected function refillBag(): void
    {
        $this->bag = array_keys($this->words);
        shuffle($this->bag);
    }

    protected function chooseNextIndex(?int $avoid = null): void
    {
        if ($avoid !== null && count($this->words) > 1) {
            $this->bag = array_values(array_diff($this->bag, [$avoid]));
        }

        if (empty($this->bag)) {
            $this->refillBag();
            if ($avoid !== null && count($this->words) > 1) {
                $this->bag = array_values(array_diff($this->bag, [$avoid]));
            }
        }

        $next = array_shift($this->bag);
        if ($next === null) $next = 0;
        $this->index = (int) $next;
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

    public function render()
    {
        return view('livewire.spelling')->layout('components.layouts.app.home', [
            'title' => 'Exercices épellation',
        ]);
    }
}