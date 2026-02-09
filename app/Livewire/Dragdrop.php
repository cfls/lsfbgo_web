<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Native\Mobile\Facades\SecureStorage;

class Dragdrop extends Component
{
    public $title = 'Jeu de lettres - Glisser-déposer';
    public $words = [];
    public $letters = [];
    public $currentWord = null;
    public $wordSlots = [];
    public $completed = false;
    public $slug;
    public $type;
    public $theme;
    public $refreshKey = 0;
    public int $completedGames = 0;
    public int $totalScore = 0;
    public bool $isComplete = false;

    public function mount()
    {


        $storedData = SecureStorage::get('data');
        $data = json_decode($storedData, true);

        // Cargar palabras
        $response = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken($data['token'])
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/spell/');



        $wordsData = $response->json('data', $response->json());

        // 🔍 Agrega este log para ver qué datos llegan
        //  \Log::info('📦 Datos de API:', ['words' => $wordsData]);

        $this->words = collect($wordsData)
            ->map(fn($w) => $w['attributes'] ?? $w)
           // ->filter(fn($word) => strlen($word['name'] ?? '') <= 6)
            ->take(5)
            ->values()
            ->toArray();

        // 🔍 Log de las palabras procesadas
        //  \Log::info('✅ Palabras procesadas:', ['words' => $this->words]);

        // 3️⃣ Cargar letras
        $responseLetters = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken($data['token'])
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/letters/');

        $lettersData = $responseLetters->json('data', $responseLetters->json());
        $this->letters = collect($lettersData)->map(function ($l) {
            $item = $l['attributes'] ?? $l;

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

        //  \Log::info("🔄 loadRandomWord() - Reseteando completed a false");

        if (empty($this->words)) {
            logger('❌ No words available in API');
            return;
        }

        $random = $this->words[array_rand($this->words)];
        $this->currentWord = $random['attributes'] ?? $random;

        //  \Log::info("📝 Nueva palabra cargada: " . ($this->currentWord['name'] ?? 'N/A'));

        $wordName = $this->currentWord['name'] ?? '';
        if ($wordName === '') {
            $this->wordSlots = [];
            return;
        }

        $normalized = $this->normalize($wordName);
        $this->wordSlots = array_fill(0, strlen($normalized), null);

        foreach ($this->letters as &$l) {
            $l['used'] = false;
        }

        $this->dispatch('$refresh');
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
        $usedCount = collect($this->wordSlots)
            ->where('symbol', $symbol)
            ->where('correct', true)
            ->count();

        if ($usedCount >= $expectedCount) {
            $this->dispatch('shake-slot', slot: $slotIndex);
            return;
        }

        if ($correct) {
            $letter = collect($this->letters)->first(fn($l) => strtolower($l['symbol']) === $symbol);

            $this->wordSlots[$slotIndex] = [
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
            $this->wordSlots[$slotIndex] = ['symbol' => $symbol, 'correct' => false];
            $this->dispatch('shake-slot', slot: $slotIndex);
        }
    }

    public function checkComplete()
    {
        if ($this->completed) {
            return;
        }

        usleep(150000);
        $allCorrect = collect($this->wordSlots)->every(fn($s) => $s && ($s['correct'] ?? false));

        if ($allCorrect) {
            $this->completed = true;

            //  \Log::info("✅ checkComplete(): Palabra completada!");

            // Incrementar AQUÍ directamente
            $this->completedGames++;

            //  \Log::info("🎮 Juegos completados: {$this->completedGames}");

            // Dispatch solo para mostrar animación
            $this->dispatch('word-completed', word: $this->currentWord['name']);

            // Si completó 5, mostrar final
            if ($this->completedGames >= 5) {
                $this->dispatch('game-completed');
            }
        }
    }

    public function nextWord()
    {
        //  \Log::info("🎮 nextWord() INICIO");

        // Resetear completed
        $this->completed = false;

        // Verificar si ya terminó
        if ($this->completedGames >= 5) {
            //  \Log::info("🏁 Ya completó 5 juegos");
            return;
        }

        //  \Log::info("📝 Cargando nueva palabra...");
        $this->loadRandomWord();
        $this->refreshKey++;
    }

    private function normalize(string $text): string
    {
        $text = strtolower(trim($text));
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return preg_replace('/[^a-z0-9]/', '', $text);
    }

    public function render()
    {
        return view('livewire.dragdrop')->layout('components.layouts.app.home', [
            'title' => 'Jeu de lettres - Drag and Drop',
        ]);
    }
}