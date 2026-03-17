<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

use Native\Mobile\Facades\SecureStorage;


class Spelling extends Component
{
    public array $words = [];
    public array $letters = [];
    public int $index = 0;
    public string $answer = '';
    public ?bool $isCorrect = null;

    public int $score = 0;          // aciertos de la ronda actual
    public int $roundTotal = 10;
    public int $total = 10; // alias
    public bool $finished = false;  // fin de ronda

    protected array $bag = [];      // bolsa de índices para evitar repeticiones inmediatas
    public array $spellings = [];
    public string $title = 'Exercices épellation';

    public function mount(ApiService $api): void
    {

        $storedData = SecureStorage::get('data');
        $data = json_decode($storedData, true);

        $response = $api->getSpellings($data['token']);



        $this->words = $response->ok()
            ? collect($response->json('data', []))
                ->pluck('attributes')
                ->where('active', 1)
                ->pluck('word')
                ->shuffle()
                ->values()
                ->toArray()
            : [];

        if (empty($this->words)) {
            $this->finished = true;
            return;
        }

        $this->refillBag();
        $this->chooseNextIndex();
        $this->recomputeLetters();
        $this->total = $this->roundTotal;
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
                // Dialog::alert('Réponse Correcte !', 'Bien joué !');  ← eliminar
            } else {
                // Dialog::alert('Réponse Incorrecte', "L'orthographe correcte est : {$this->currentWord}"); ← eliminar
            }
        }

    public function next(): void
    {
        if ($this->isCorrect === null) {
            $this->checkAnswer();
        }
        if ($this->isCorrect !== true) {
            return; // solo avanza si es correcto
        }

        // ¿Ya alcanzamos 5 ejercicios correctos?
        if ($this->score >= $this->roundTotal) {
            $this->finished = true;
            $this->answer = '';
            $this->isCorrect = null;
            $this->letters = [];     // limpia el player
            return;
        }

        // Elegir siguiente palabra aleatoria sin repetir inmediata
        $this->chooseNextIndex($this->index);

        $this->answer = '';
        $this->isCorrect = null;
        $this->recomputeLetters();
    }

    // "Rejouer"/"Nuevo": nueva ronda (reinicia progreso de la ronda a 0)
    public function restart(): void
    {
        $this->score = 0;
        $this->finished = false;
        $this->answer = '';
        $this->isCorrect = null;

        $this->refillBag();
        $this->chooseNextIndex();   // arranca aleatorio
        $this->recomputeLetters();
    }

    protected function refillBag(): void
    {
        $this->bag = array_keys($this->words); // [0,1,2,...]
        shuffle($this->bag);
    }

    protected function chooseNextIndex(?int $avoid = null): void
    {
        // Evitar repetir la palabra actual si hay alternativas
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
        if ($next === null) $next = 0; // fallback
        $this->index = (int) $next;
    }

    protected function recomputeLetters(): void
    {
        if (!$this->currentWord) {
            $this->letters = [];
            return;
        }

        // Normalizar palabra para eliminar tildes y caracteres especiales
        $normalized = $this->normalize($this->currentWord);

        // Generar letras del string normalizado
        $this->letters = preg_split('//u', (string)$normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
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
