<?php

namespace App\Livewire;

use AllowDynamicProperties;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Browser;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;

#[AllowDynamicProperties]
class SignTypeQuiz extends Component
{
    public $questions = []; // ✅ TODAS las preguntas cargadas UNA SOLA VEZ
    public $currentIndex = 0;
    public $message = '';
    public $image;
    public $answered = false;
    public $isCorrect = false;
    public $userAnswer = '';
    public $selectedAnswer = '';
    public $userInput = '';
    public $completed = false;
    public $score = 0;
    public $slug;
    public $type;
    public $slug_theme;
    public $hasSubscription = false;
    public $currentQuestion;
    public $selectedLink;
    public $totalQuestions;
    public $storedData;

    protected $listeners = [
        'match-answered' => 'onMatchAnswered',
    ];

    public function mount()
    {


        $this->hasSubscription = false;
        $this->checkUserSubscription();

        // ✅ Cargar preguntas UNA SOLA VEZ
        if (empty($this->questions)) {
            $this->loadQuestions();
        }

        // ✅ Inicializar currentQuestion
        if (!empty($this->questions)) {
            $this->currentQuestion = $this->questions[0];
        }
    }

    protected function loadQuestions()
    {

        try {
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
               // ->withToken(SecureStorage::get('data.token'))
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/questions/' . $this->slug . '/' . $this->slug_theme . '?type=' . $this->type);

            if ($response->successful()) {
                $data = $response->json('data', []);

                // ✅ Mezclar y guardar TODAS las preguntas
                shuffle($data);
                $this->questions = $data;

                // ✅ Log para verificar
              //  logger()->info('Preguntas tipo cargadas: ' . count($this->questions));
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

        if ($correct) {
            $this->score += 10;
            $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
            $this->js('setTimeout(() => $wire.nextStep(), 1500)');
        } else {
            $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="mal" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
            $this->js('setTimeout(() => $wire.nextStep(), 3000)');
        }
    }

    public function getTotalQuestionsProperty()
    {
        return count($this->questions);
    }

    protected function checkUserSubscription(): void
    {
        try {
            $storedData = SecureStorage::get('data');
            $data = json_decode($storedData, true);

            if (!$data['user']['id'] || !$data['token']) {
                return;
            }

            $url = config('services.api.url') . '/v1/verify-codes/' . $data['user']['id'];

            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($data['token'])
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                $subscriptionData = $response->json('data', []);

                foreach ($subscriptionData as $sub) {
                    if ($sub['attributes']['theme'] === $this->slug) {
                        if ($sub['attributes']['active'] === 1) {
                            $this->hasSubscription = true;
                            return;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Error al verificar la suscripción: ' . $e->getMessage());
        }
    }

    public function selectAnswer($answer)
    {
        $this->selectedAnswer = $answer;

        $current = $this->questions[$this->currentIndex] ?? null;

        if ($current && in_array($current['type'], ['video-choice', 'choice', 'yes-no'])) {
            $correctAnswer = strtolower($this->normalizeAnswer($current['answer']));
            $givenAnswer = strtolower($this->normalizeAnswer($answer));

            $this->answered = true;

            if ($givenAnswer === $correctAnswer) {
                $this->isCorrect = true;
                $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-20 p-5 object-cover dark:bg-gray-200 rounded-full" />';
                $this->score += 10;
                $this->js('setTimeout(() => $wire.nextStep(), 1500)');
            } else {
                $this->isCorrect = false;
                $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="mal" class="w-32 h-32 object-contain p-5 dark:bg-gray-200 rounded-full" />';
                $this->message = $correctAnswer;
                $this->js('setTimeout(() => $wire.nextStep(), 3000)');

            }
        }
    }

    public function checkAnswer()
    {
        if (empty($this->userInput)) {
            return;
        }

        $this->answered = true;

        $current = $this->questions[$this->currentIndex];

        $validAnswers = array_map(
            fn($a) => strtolower($this->normalizeAnswer(trim($a))),
            explode('/', $current['answer'])
        );

        $userAnswers = array_map(
            fn($a) => strtolower($this->normalizeAnswer(trim($a))),
            explode('/', $this->userInput)
        );

        $isValid = false;

        foreach ($userAnswers as $ans) {
            if (in_array($ans, $validAnswers, true)) {
                $isValid = true;
                break;
            }
        }

       

        if ($isValid) {
            $this->isCorrect = true;
            $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-32 h-32 object-contain p-5 dark:bg-gray-200 rounded-full" />';
            $this->score += 10;
            $this->js('setTimeout(() => $wire.nextStep(), 1500)');
        } else {
            $this->isCorrect = false;
            $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="mal" class="w-32 h-32 object-contain p-5 dark:bg-gray-200 rounded-full" />';
            $this->message = implode(' / ', $validAnswers);
            $this->js('setTimeout(() => $wire.nextStep(), 3000)');

            }
    }

    public function nextStep()
    {

        $data = json_decode(SecureStorage::get('data'), true);

        // ✅ Verificar suscripción
        if ($this->currentIndex == 2 && !$this->hasSubscription) {
            $syllabusResponse = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(SecureStorage::get($data['token']))
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/syllabus/settings/' . $this->slug);




            $this->syllabusData = $syllabusResponse->json('data', []);

            $link = $this->syllabusData['attributes']['link'] ?? config('app.site');

            $this->openPaymentModal($link);
            return;
        }

        // ✅ Avanzar a la siguiente pregunta (SIN CARGAR MÁS DATOS)
        if ($this->currentIndex < count($this->questions) - 1) {
            $this->dispatch('next-step');

            // ✅ Simplemente incrementar el índice
            $this->currentIndex++;

            // ✅ Obtener la pregunta actual del array existente
            $this->currentQuestion = $this->questions[$this->currentIndex];

            $this->resetQuestionState();

            // ✅ Actualizar video si existe
            if (!empty($this->currentQuestion['video'])) {
                $this->dispatch('quiz-video-update', publicId: $this->currentQuestion['video']);
            }

            // ✅ Log para depuración
            logger()->info("Pregunta tipo {$this->currentIndex} de " . count($this->questions));
        } else {
            // ✅ Completar el quiz
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
        )->id('alert-demo');
    }

    #[OnNative(ButtonPressed::class)]
    public function handleAlert(int $index, string $id): void
    {
        if ($id === 'alert-demo' && $index === 0 && $this->selectedLink) {
            Browser::open($this->selectedLink);
        }
    }

    protected function resetQuestionState()
    {
        $this->message = '';
        $this->image = null;
        $this->answered = false;
        $this->isCorrect = false;
        $this->userInput = '';
        $this->selectedAnswer = ''; // ✅ Añadir esto también
    }

    public function completed()
    {
        $total = count($this->questions) * 10;
        $percentage = ($this->score / $total) * 100;

        if ($percentage < 80) {
            $this->dispatch('quiz-failed', percentage: round($percentage, 2));
            return;
        }


            $this->saveQuizResult();


        $this->dispatch('quiz-finished', [
            'score' => $this->score,
            'total' => $total
        ]);
    }

    protected function saveQuizResult()
    {
        try {

            $data = json_decode(SecureStorage::get('data'), true);

            $checkUrl = sprintf(
                '%s/v1/quiz-results/check/%s/%s/%s/%s',
                config('services.api.url'),
                $data['user']['id'],
                $this->slug,
                $this->slug_theme,
                $this->type
            );

            $checkResponse = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($data['token'])
                ->acceptJson()
                ->get($checkUrl);

            if ($checkResponse->successful()) {
                $data = $checkResponse->json('data', []);
                if (!empty($data)) {
                    return;
                }
            }
            $secure = SecureStorage::get('data');
            $data = json_decode($secure, true);

            Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($data['token'])
                ->acceptJson()
                ->post(config('services.api.url') . '/v1/quiz-results', [
                    'user_id'   => $data['user']['id'],
                    'syllabus'  => $this->slug,
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
        return !empty(trim($this->userInput));
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

    public function restartQuiz()
    {
        $this->currentIndex = 0;
        $this->score = 0;
        $this->completed = false;

        // ✅ Volver a cargar y mezclar las preguntas
        $this->loadQuestions();
        $this->resetQuestionState();

        if (!empty($this->questions)) {
            $this->currentQuestion = $this->questions[0];
        }
    }

    public function submitFeedback($feedbackData)  // ✅ Recibe array, no Request
    {

        $api = app(ApiService::class);

        logger()->info('🔵 Feedback received:', ['feedback_data' => $feedbackData]);

        try {
            // Validar los datos del feedback que vienen del frontend
            $validatedFeedback = validator($feedbackData, [
                'type' => 'required|in:bug,suggestion,question',
                'message' => 'required|string|max:1000',
                'question_id' => 'nullable|integer',

            ])->validate();

            logger()->info('✅ Feedback validation passed:', $validatedFeedback);

            // Obtener datos del usuario de SecureStorage
            $storedData = SecureStorage::get('data');
            $userData = json_decode($storedData, true);


            logger()->info('👤 User data loaded:', [
                'user_id' => $userData['user']['id'] ?? 'null'
            ]);

            $completeData = [
                    'user_id' => $userData['user']['id'] ?? null,
                    'type' => $validatedFeedback['type'],
                    'message' => $validatedFeedback['message'],
                    'question_id' => $validatedFeedback['question_id'] ?? null,
                    'status' => 'pending',
                ];

            logger()->info('📦 Sending to API:', $completeData);

            // ✅ Llamar a la API con el array completo
            $result = $api->FeedBack($completeData);

            logger()->info('✅ API response:', ['result' => $result]);

            logger()->info('🎉 Feedback saved successfully!');

            return [
                'success' => true,
                'message' => 'Feedback received successfully'
            ];

        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->error('❌ Validation failed:', [
                'errors' => $e->errors()
            ]);
            throw $e;

        } catch (\Exception $e) {
            logger()->error('❌ Exception:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }

    public function render()
    {
        // ✅ Obtener la pregunta actual del array existente
        $this->currentQuestion = $this->questions[$this->currentIndex] ?? null;

        return view('livewire.sign-type-quiz', [
            'currentQuestion' => $this->currentQuestion,
            'score' => $this->score,
            'currentIndex' => $this->currentIndex,
            'questions' => $this->questions,
            'totalQuestions' => $this->totalQuestions,
        ]);
    }
}