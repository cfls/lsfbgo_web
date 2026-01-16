<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class SignVideoMatch extends Component
{
    public $pairs = [];

    public $pairsWords = [];
    public $pairsVideos = [];

    public $tableRows = [];

    public $selectedMatches = [];
    public $selectedWord = null;
    public $selectedVideo = null;

    public $wrongWord = null;
    public $wrongVideo = null;

    public $answered = false;
    public $isCorrect = false;
    public $correctCount;

    public $score = 0;

    protected $listeners = [
        'clear-wrong' => 'clearWrong'
    ];

    public function mount($pairs = [])
    {
        $pairs = $this->cleanJson($pairs);

        $pairs = collect($pairs)->filter(fn ($p) =>
            isset($p['word']) && isset($p['video'])
        )->unique('word')->values();

        $this->pairs = $pairs->toArray();

        // columna palabras mezcladas
        $this->pairsWords = $pairs->pluck('word')->shuffle()->values()->toArray();

        // columna videos mezclados
        $this->pairsVideos = $pairs->shuffle()->values()->toArray();

        $this->resetQuiz();
    }

    public function selectWord($word)
    {
        if ($this->answered) return;

        $this->selectedWord = $word;
        $this->tryValidate();

        // NO re-renderizar, solo actualizar estado
        $this->skipRender();
    }

    public function selectVideo($videoWord)
    {
        if ($this->answered) return;

        $this->selectedVideo = $videoWord;
        $this->tryValidate();

        // NO re-renderizar, solo actualizar estado
        $this->skipRender();
    }

    public function tryValidate()
    {
        if (!$this->selectedWord || !$this->selectedVideo) return;
        $this->validatePair();
    }

    public function validatePair()
    {
        $word  = $this->selectedWord;
        $video = $this->selectedVideo;

        $isCorrectPair = ($word === $video);

        if ($isCorrectPair) {
            $this->selectedMatches[$word] = $video;

            // Enviar evento para actualizar Alpine sin re-render
            $this->dispatch('match-updated', matches: $this->selectedMatches);
        } else {
            $this->wrongWord = $word;
            $this->wrongVideo = $video;
            $this->dispatch('wrong-match');
        }

        // Resetear selecciones
        $this->selectedWord = null;
        $this->selectedVideo = null;

        if ($isCorrectPair) {
            $this->checkIfComplete();
        }
    }

    #[On('clear-wrong')]
    public function clearWrong()
    {
        $this->wrongWord = null;
        $this->wrongVideo = null;
    }

    public function checkIfComplete()
    {
        if (count($this->selectedMatches) < count($this->pairs)) {
            return;
        }

        $this->correctCount = 0;

        foreach ($this->pairs as $pair) {
            $word = $pair['word'];
            $userAnswer = $this->selectedMatches[$word] ?? null;

            if ($userAnswer === $word) {
                $this->correctCount++;
            }
        }

        $this->score = $this->correctCount * 10;
        $this->answered = true;
        $this->isCorrect = ($this->correctCount === count($this->pairs));

        $this->dispatch('match-answered', correct: $this->isCorrect);

        // Aquí SÍ necesitas re-renderizar para mostrar resultados
        // pero los videos ya no estarán en pantalla
    }

    public function resetQuiz()
    {
        $this->selectedMatches = [];
        $this->selectedWord = null;
        $this->selectedVideo = null;
        $this->wrongWord = null;
        $this->wrongVideo = null;
        $this->answered = false;
        $this->isCorrect = false;
    }

    private function cleanJson($value)
    {
        // Si es array, regresarlo tal cual
        if (is_array($value)) {
            return $value;
        }

        // Si ya decodifica, bien
        $first = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $first;
        }

        // Intentar quitar slashes internos
        $value2 = stripslashes($value);
        $second = json_decode($value2, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $second;
        }

        // Intento final: quitar comillas exteriores
        $trim = trim($value, '"');
        $third = json_decode($trim, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $third;
        }

        return [];
    }

    public function render()
    {
        return view('livewire.sign-video-match');
    }
}