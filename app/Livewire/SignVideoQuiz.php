<?php

namespace App\Livewire;

use AllowDynamicProperties;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Browser;
use Native\Mobile\Facades\Dialog;

#[AllowDynamicProperties]
class SignVideoQuiz extends Component
{
    public $questions = [];
    public $currentIndex = 0;
    public $message = '';
    public $image;
    public $answered = false;
    public $isCorrect = false;
    public $userAnswer = '';
    public $selectedAnswer = '';
    public $correctAnswer = '';
    public $userInput = '';
    public $completed = false;
    public $score = 0; // ✅ Iniciar en 0 es más correcto
    public $slug;
    public $type;
    public $slug_theme;
    public $hasSubscription = false;
    public $selectedSyllabuForPayment;
    public $currentQuestion;
    public $selectedLink;

    protected $listeners = [
        'match-answered' => 'onMatchAnswered',
    ];

    public function mount()
    {
        $this->hasSubscription = false;
        $this->checkUserSubscription();

        if (empty($this->questions)) {
            $this->loadQuestions();
        }

        if (empty($this->questions)) {
            $this->questions = [];
            $this->completed = false;
        }
    }

    /**
     * 🆕 Método separado para cargar preguntas
     */
    protected function loadQuestions()
    {
        try {
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(session('data.token'))
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/questions/' . $this->slug . '-themes/' . $this->slug_theme);



            if ($response->successful()) {
                $data = $response->json('data', []);

                // ✅ Mezclar y tomar 15
                shuffle($data);
                $this->questions = array_slice($data, 0, 15);
            }
        } catch (\Throwable $e) {
            logger()->error('Error cargando preguntas: ' . $e->getMessage());
            $this->questions = [];
        }
    }

    public function onMatchAnswered($correct)
    {
        $this->answered = true;
        $this->isCorrect = $correct;

        // 🆕 Sumar puntos también para match
        if ($correct) {
            $this->score += 10;
            $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
        } else {
            $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="mal" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
        }
    }

    public function getTotalQuestionsProperty()
    {
        return count($this->questions);
    }

    protected function checkUserSubscription(): void
    {
        try {
            $userId = session('data.user.id');
            $token = session('data.token');

            if (!$userId || !$token) {
                logger()->warning('Sesión no válida: usuario o token faltante.');

               // $this->hasSubscription = false;
                return;
            }

            $url = config('services.api.url') . '/v1/verify-codes/' . $userId;

            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($token)
                ->acceptJson()
                ->get($url);



            if ($response->successful()) {
                $subscriptionData = $response->json('data', []);



                foreach ($subscriptionData as $sub) {
                    if ($sub['attributes']['theme'] === $this->slug . '-themes') {
                        if ($sub['attributes']['active'] === 1) {
                            $this->hasSubscription = true;
                            return;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Error al verificar la suscripción: ' . $e->getMessage());

            // $this->hasSubscription = false;
        }
    }

    public function selectAnswer($answer)
    {
        $this->selectedAnswer = $answer;

        $current = $this->questions[$this->currentIndex] ?? null;

        if ($current && $current['type'] === 'video-choice') {
            $correctAnswer = strtolower($this->normalizeAnswer($current['answer']));
            $givenAnswer = strtolower($this->normalizeAnswer($answer));

            $this->answered = true;

            if ($givenAnswer === $correctAnswer) {
                $this->isCorrect = true;
                $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
                $this->score += 10;
            } else {
                $this->isCorrect = false;
                $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="mal" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
                $this->message = $correctAnswer;
            }
        }
    }

    public function checkAnswer()
    {

        $this->answered = true;

        $current = $this->questions[$this->currentIndex];

        // Obtener todas las respuestas correctas
        $validAnswers = array_map(
            fn($a) => strtolower($this->normalizeAnswer(trim($a))),
            explode('/', $current['answer'])
        );

        // Respuesta dada por usuario
        $givenAnswer = strtolower(
            $this->normalizeAnswer(
                $current['type'] === 'text'
                    ? $this->userInput
                    : $this->selectedAnswer
            )
        );

        // Usuario puede escribir varias opciones: ej. "jusque / jusqu'a"
        $userAnswers = array_map(
            fn($a) => strtolower($this->normalizeAnswer(trim($a))),
            explode('/', $givenAnswer)
        );

        $isValid = false;

        foreach ($userAnswers as $ans) {
            if (in_array($ans, $validAnswers, true)) {
                $isValid = true;
                break;
            }
        }

        // Resultado
        if ($isValid) {
            $this->isCorrect = true;
            $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
            $this->score += 10;
        } else {
            $this->isCorrect = false;
            $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="mal" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
            $this->message = implode(' / ', $validAnswers);
        }
    }

    public function nextStep()
    {


        // 🆕 Verificar suscripción en la pregunta 2 (índice 1)
        if ($this->currentIndex == 2 && !$this->hasSubscription) {
            // 🎯 DISPARAR EVENTO: subscription-required



            switch ($this->slug) {
                case 'ue1':
                    $link = "https://cfls.be/boutique/syllabus-1";
                    break;
                case 'ue2':
                    $link = "https://cfls.be/boutique/syllabus-2";
                    break;
                case 'ue3':
                    $link = "https://cfls.be/boutique/syllabus-3";
                    break;
                default:
                    $link = "https://cfls.be/boutique";
            }

            $this->openPaymentModal($link);
            return;
        }

        // Verificar si hay más preguntas
        if ($this->currentIndex < count($this->questions) - 1) {

            // 🎯 DISPARAR EVENTO: next-step (para animación)
            $this->dispatch('next-step');

            // Avanzar
            $this->currentIndex++;
            $this->currentQuestion = $this->questions[$this->currentIndex];

            // Limpiar estado
            $this->resetQuestionState();

            // 🎯 Emitir evento para actualizar videos
            if (!empty($this->currentQuestion['video'])) {
                $this->dispatch('quiz-video-update', publicId: $this->currentQuestion['video']);
            } else {
                $this->dispatch('quiz-video-refresh');
            }
        } else {
            // Terminar quiz
            $this->completed();
        }
    }

    public function openPaymentModal($link)
    {

        $this->selectedLink = $link;

        Dialog::alert(
            'Accès Syllabus',
            'Ce contenu nécessite l\'achat du livre Syllabus. Voulez-vous ouvrir la boutique maintenant?',
            [
                'Oui, ouvrir la boutique',
                'Non, plus tard'
            ]
        )->id('alert-demo');;


    }

    #[OnNative(ButtonPressed::class)]
    public function handleAlert(int $index, string $id): void
    {
        if ($id === 'alert-demo' && $index === 0 && $this->selectedLink) {
            Browser::open($this->selectedLink);
        }
    }

    /**
     * 🆕 Método para resetear estado de pregunta
     */
    protected function resetQuestionState()
    {
        $this->message = '';
        $this->image = null;
        $this->answered = false;
        $this->isCorrect = false;
        $this->selectedAnswer = '';
        $this->userInput = '';
        $this->userAnswer = '';
    }

    public function completed()
    {
        $total = count($this->questions) * 10;
        $percentage = ($this->score / $total) * 100;

        // ❌ Si no alcanza el 80%, falló
        if ($percentage < 80) {
            // 🎯 DISPARAR EVENTO: quiz-failed
            $this->dispatch('quiz-failed', percentage: round($percentage, 2));
            return;
        }

        // ✅ Guardar resultado si tiene token
        if (session('data.token')) {
            $this->saveQuizResult();
        }

        // 🎯 DISPARAR EVENTO: quiz-finished
        $this->dispatch('quiz-finished');
    }

    /**
     * 🆕 Método separado para guardar resultado
     */
    protected function saveQuizResult()
    {
        try {
            $token = session('data.token');
            $userId = session('data.user.id');

            // Verificar si ya existe
            $checkUrl = sprintf(
                '%s/v1/quiz-results/check/%s/%s/%s/%s',
                config('services.api.url'),
                $userId,
                $this->slug . '-themes',
                $this->slug_theme,
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
                    // Ya existe, no guardar
                    return;
                }
            }

            // Guardar nuevo resultado
            Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($token)
                ->acceptJson()
                ->post(config('services.api.url') . '/v1/quiz-results', [
                    'user_id'   => $userId,
                    'syllabus'  => $this->slug . '-themes',
                    'theme'     => $this->slug_theme,
                    'type'      => $this->type,
                    'score'     => $this->score,
                    'played_at' => now()->toDateString(),
                ]);

        } catch (\Throwable $e) {
            logger()->error('Error guardando resultado del quiz: ' . $e->getMessage());
        }
    }

    public function getCanValidateProperty(): bool
    {
        $current = $this->questions[$this->currentIndex] ?? null;

        if (!$current) {
            return false;
        }

        if ($current['type'] === 'text') {
            return !empty(trim($this->userInput));
        }

        return !empty($this->selectedAnswer);
    }

    private function normalizeAnswer($text)
    {
        $text = strtolower(trim($text));

        $text = str_replace(
            ['á', 'à', 'ä', 'â', 'ã', 'å', 'æ',
                'ç',
                'é', 'è', 'ë', 'ê',
                'í', 'ì', 'ï', 'î',
                'ñ',
                'ó', 'ò', 'ö', 'ô', 'õ', 'œ',
                'ú', 'ù', 'ü', 'û',
                'ý', 'ÿ'],
            ['a','a','a','a','a','a','ae',
                'c',
                'e','e','e','e',
                'i','i','i','i',
                'n',
                'o','o','o','o','o','oe',
                'u','u','u','u',
                'y','y'],
            $text
        );

        return $text;
    }

    public function closePaymentModal()
    {
        $this->redirect('/syllabus');
    }

    /**
     * 🆕 Método para reiniciar el quiz
     */
    public function restartQuiz()
    {
        $this->currentIndex = 0;
        $this->score = 0;
        $this->completed = false;

        // Recargar preguntas
        $this->loadQuestions();

        // Resetear estado
        $this->resetQuestionState();

        // Cargar primera pregunta
        if (!empty($this->questions)) {
            $this->currentQuestion = $this->questions[0];
        }
    }

    public function render()
    {
        $this->currentQuestion = $this->questions[$this->currentIndex] ?? null;



        return view('livewire.sign-video-quiz', [
            'currentQuestion' => $this->currentQuestion,
            'score' => $this->score
        ]);
    }
}