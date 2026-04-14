<?php

namespace App\Livewire;

use AllowDynamicProperties;
use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

#[AllowDynamicProperties]
class SignTypeQuiz extends Component
{
    public $questions = [];
    public $currentIndex = 0;
    public int $currentQuestionId = 0;
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
    public bool $showPaymentModal = false;
    public string $accessCode = '';
    public string $link = '';
    public string $theme = '';
    public $syllabusData = [];

    protected $listeners = [
        'match-answered' => 'onMatchAnswered',
    ];

    public function mount()
    {
        $this->storedData = session('data');

        if (!$this->storedData || empty(session('token'))) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        $this->hasSubscription = false;
        $this->checkUserSubscription();

        if (empty($this->questions)) {
            $this->loadQuestions();
        }

        if (!empty($this->questions)) {
            $this->currentQuestion = $this->questions[0];
            $this->currentQuestionId = $this->currentQuestion['id'] ?? 0;
        }
    }

    protected function loadQuestions()
    {
        try {
            $token = session('token');

            if (!$token) {
                $this->questions = [];
                return;
            }

            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
                'timeout' => 30,
                'connect_timeout' => 10,
            ])
                ->withToken($token)
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/questions/' . $this->slug . '/' . $this->slug_theme . '?type=' . $this->type);

            if ($response->successful()) {
                $data = $response->json('data', []);
                shuffle($data);
                $this->questions = $data;
            }
        } catch (\Throwable $e) {
            $this->questions = [];
        }
    }

    public function onMatchAnswered($correct)
    {
        $this->answered = true;
        $this->isCorrect = $correct;

        if ($correct) {
            $this->score += 10;
            $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-40 h-40 object-contain dark:bg-gray-200 rounded-full" />';
        } else {
            $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="mal" class="w-40 h-40 object-contain dark:bg-gray-200 rounded-full" />';
        }
    }

    public function getTotalQuestionsProperty()
    {
        return count($this->questions);
    }

    protected function checkUserSubscription(): void
    {
        try {
            $data = session('data');
            $token = session('token');

            if (!$data || !$token || empty($data['user']['id'])) {
                return;
            }

            $url = config('services.api.url') . '/v1/verify-codes/' . $data['user']['id'];

            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
                'timeout' => 30,
                'connect_timeout' => 10,
            ])
                ->withToken($token)
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                $subscriptionData = $response->json('data', []);

                foreach ($subscriptionData as $sub) {
                    if (
                        ($sub['attributes']['theme'] ?? null) === $this->slug &&
                        ($sub['attributes']['active'] ?? 0) === 1
                    ) {
                        $this->hasSubscription = true;
                        return;
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
                $this->image = true;
                $this->score += 10;
            } else {
                $this->isCorrect = false;
                $this->image = true;
                $this->message = $correctAnswer;
            }
        }
    }

    public function checkAnswer()
    {
        if (empty($this->userInput)) {
            return;
        }

        $this->answered = true;

        $current = $this->questions[$this->currentIndex] ?? null;

        if (!$current) {
            return;
        }

        $validAnswers = array_map(
            fn ($a) => mb_strtolower(trim($a), 'UTF-8'),
            explode(' / ', $current['answer'])
        );

        $userAnswers = array_map(
            fn ($a) => mb_strtolower(trim($a), 'UTF-8'),
            explode(' / ', $this->userInput)
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
            $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-40 h-40 object-contain p-5 dark:bg-gray-200 rounded-full" />';
            $this->score += 10;
        } else {
            $this->isCorrect = false;
            $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="mal" class="w-40 h-40 object-contain p-5 dark:bg-gray-200 rounded-full" />';
            $this->message = implode(' / ', $validAnswers);
        }
    }

    public function nextStep()
    {
        $data = session('data');
        $token = session('token');

        if (!$data || !$token) {
            $this->redirect(route('home'), navigate: true);
            return;
        }

        if ($this->currentIndex == 2 && !$this->hasSubscription) {
            $syllabusResponse = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
                'timeout' => 30,
                'connect_timeout' => 10,
            ])
                ->withToken($token)
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/syllabus/settings/' . $this->slug);

            $this->syllabusData = $syllabusResponse->json('data', []);
            $link = $this->syllabusData['attributes']['link'] ?? config('app.site');

            $this->openPaymentModal($link, $this->slug);
            return;
        }

        if ($this->currentIndex < count($this->questions) - 1) {
            $this->dispatch('next-step');

            $this->currentIndex++;
            $this->currentQuestion = $this->questions[$this->currentIndex];
            $this->currentQuestionId = $this->currentQuestion['id'] ?? 0;

            $this->resetQuestionState();

            if (!empty($this->currentQuestion['video'])) {
                $this->dispatch('quiz-video-update', publicId: $this->currentQuestion['video']);
            }
        } else {
            $this->completed();
        }
    }

    public function openPaymentModal($link, $theme = null): void
    {
        $this->theme = $theme;
        $this->selectedLink = $link;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->redirectRoute('games');
    }

    public function openShop()
    {
        if ($this->selectedLink) {
            return redirect()->away($this->selectedLink);
        }
    }

    public function validateCode(): void
    {
        $this->validate([
            'accessCode' => ['required', 'string'],
        ]);

        $api = app(ApiService::class);
        $data = session('data');

        if (!$data || empty($data['user']['id'])) {
            $this->addError('accessCode', 'Session invalide');
            return;
        }

        $user = $data['user'];

        $verifyUser = $api->Code($user['id'], $this->accessCode, $this->theme);

        if ($verifyUser->successful() && $verifyUser->json('data.attributes.active') === 1) {
            $this->showPaymentModal = false;
            $this->accessCode = '';
            $this->hasSubscription = true;
        } else {
            $this->addError('accessCode', 'Code invalide');
        }
    }

    protected function resetQuestionState()
    {
        $this->message = '';
        $this->image = null;
        $this->answered = false;
        $this->isCorrect = false;
        $this->userInput = '';
        $this->selectedAnswer = '';
        $this->currentQuestionId = $this->currentQuestion['id'] ?? 0;
    }

    public function completed()
    {
        $total = count($this->questions) * 10;
        $percentage = $total > 0 ? ($this->score / $total) * 100 : 0;

        if ($percentage < 80) {
            $this->dispatch('quiz-failed', percentage: round($percentage, 2));
            return;
        }

        $this->saveQuizResult();

        $this->dispatch('quiz-finished', [
            'score' => $this->score,
            'total' => $total,
        ]);
    }

    protected function saveQuizResult()
    {
        try {
            $data = session('data');
            $token = session('token');

            if (!$data || !$token || empty($data['user']['id'])) {
                return;
            }

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
                'timeout' => 30,
                'connect_timeout' => 10,
            ])
                ->withToken($token)
                ->acceptJson()
                ->get($checkUrl);

            if ($checkResponse->successful()) {
                $existing = $checkResponse->json('data', []);
                if (!empty($existing)) {
                    return;
                }
            }

            Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
                'timeout' => 30,
                'connect_timeout' => 10,
            ])
                ->withToken($token)
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
            [
                'á', 'à', 'ä', 'â', 'ã', 'å', 'æ',
                'ç',
                'é', 'è', 'ë', 'ê',
                'í', 'ì', 'ï', 'î',
                'ñ',
                'ó', 'ò', 'ö', 'ô', 'õ', 'œ',
                'ú', 'ù', 'ü', 'û',
                'ý', 'ÿ'
            ],
            [
                'a', 'a', 'a', 'a', 'a', 'a', 'ae',
                'c',
                'e', 'e', 'e', 'e',
                'i', 'i', 'i', 'i',
                'n',
                'o', 'o', 'o', 'o', 'o', 'oe',
                'u', 'u', 'u', 'u',
                'y', 'y'
            ],
            $text
        );

        return $text;
    }

    public function restartQuiz()
    {
        $this->currentIndex = 0;
        $this->score = 0;
        $this->completed = false;

        $this->loadQuestions();
        $this->resetQuestionState();

        if (!empty($this->questions)) {
            $this->currentQuestion = $this->questions[0];
            $this->currentQuestionId = $this->currentQuestion['id'] ?? 0;
        }
    }

    public function submitFeedback($feedbackData)
    {
        $api = app(ApiService::class);

        try {
            $validatedFeedback = validator($feedbackData, [
                'type' => 'required|in:bug,suggestion,question',
                'message' => 'required|string|max:1000',
                'question_id' => 'nullable|integer',
            ])->validate();

            $data = session('data');

            $completeData = [
                'user_id' => $data['user']['id'] ?? null,
                'type' => $validatedFeedback['type'],
                'message' => $validatedFeedback['message'],
                'question_id' => $validatedFeedback['question_id'] ?? null,
                'status' => 'pending',
            ];

            $api->FeedBack($completeData);

            return [
                'success' => true,
                'message' => 'Feedback received successfully',
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->error('Validation failed:', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            logger()->error('Exception:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            throw $e;
        }
    }

    public function render()
    {
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