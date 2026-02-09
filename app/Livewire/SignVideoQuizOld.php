<?php

namespace App\Livewire;

use AllowDynamicProperties;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Native\Mobile\Facades\SecureStorage;

#[AllowDynamicProperties]
class SignVideoQuizOld extends Component
{
    public $questions = [];
    public $currentIndex = 0; // Empezar en la pregunta 2 (índice 1)

    public $message = '';
    public $image;
    public $answered = false;
    public $isCorrect = false;
    public $userAnswer = '';
    public $selectedAnswer = '';
    public $correctAnswer = '';
    public $userInput = '';
    public $completed = false;
    public $score = 1;
    public $slug;
    public $type;
    public $slug_theme;
    public $hasSubscription = false;
    public $selectedSyllabuForPayment;
    public $currentQuestion;      // Current question data


    protected $listeners = [
        'match-answered' => 'onMatchAnswered',
    ];

    public function mount()
    {

        $this->hasSubscription = false; // Valor por defecto
        $this->checkUserSubscription();



        // ⚠️ Solo cargar preguntas si no están ya definidas
        if (empty($this->questions)) {
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(SecureStorage::get('data.token'))
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/questions/'.  $this->slug .'-themes' .'/'. $this->slug_theme);

            $data = $response->json('data', []);


            // ✅ Mezclar al azar
            shuffle($data);


            // ✅ Tomar solo 15 preguntas
            $this->questions = array_slice($data, 0, 15);



        }

        // Seguridad: si no trae nada, evitamos crash
        if (empty($this->questions)) {
            $this->questions = [];
            $this->completed = false;
            //$this->message = "⚠️ No se encontraron preguntas disponibles.";
        }
    }

    public function onMatchAnswered($correct)
    {
        $this->answered = true;
        $this->isCorrect = $correct;
    }

    public function getTotalQuestionsProperty()
    {
        return count($this->questions);
    }

    protected function checkUserSubscription(): void
    {
        try {
            $userId = SecureStorage::get('data.user.id');
            $token = SecureStorage::get('data.token');

            if (!$userId || !$token) {
                logger()->warning('Sesión no válida: usuario o token faltante.');
                $this->hasSubscription = false;
                return;
            }

            $url = config('services.api.url') . '/v1/verify-codes/' . session('data.user.id');

            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($token)
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                $subscriptionData = $response->json('data', []);


                foreach ($subscriptionData as $sub) {
                    if ($sub['attributes']['theme'] === $this->slug .'-themes') {
                        if ($sub['attributes']['status'] === 1) {
                            $this->hasSubscription = true;
                            return;
                        }
                    }
                }


            } else {
                logger()->warning('No se pudo verificar la suscripción. Código: ' . $response->status());
            }
        } catch (\Throwable $e) {
            logger()->error('Error al verificar la suscripción: ' . $e->getMessage());
            $this->hasSubscription = false;
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
                //  $this->image = '<img src="' . asset('/img/femme_bon.png') . '" alt="bon" class="w-20 object-cover" /> ';
                $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-12 object-cover" /> ';
                $this->score += 10;

            } else {

                $this->isCorrect = false;
                // $this->image = '<img src="' . asset('/img/femme_eche.png') . '" alt="eche" class="w-20 object-cover" />';
                $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="bon" class="w-12 object-cover" /> ';
                $this->message = $correctAnswer;


            }
        }
    }


    public function checkAnswer()
    {
        $this->answered = true;

        $current = $this->questions[$this->currentIndex];

        // Obtener todas las respuestas correctas como lista
        $validAnswers = array_map(
            fn($a) => strtolower($this->normalizeAnswer(trim($a))),
            explode('/', $current['answer'])
        );

        // respuesta dada por usuario
        $givenAnswer = strtolower(
            $this->normalizeAnswer(
                $current['type'] === 'text'
                    ? $this->userInput
                    : $this->selectedAnswer
            )
        );

        // Si el usuario escribió varias opciones: ej. "jusque / jusqu'a"
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

        // Resultado de la validación
        if ($isValid) {
            $this->isCorrect = true;
            $this->image = '<img src="' . asset('/img/lsfgo/good.png') . '" alt="bon" class="w-12 object-cover" />';
            $this->score += 10;
        } else {
            $this->isCorrect = false;
            $this->image = '<img src="' . asset('/img/lsfgo/bad.png') . '" alt="bon" class="w-12 object-cover" />';
            $this->message = implode(' / ', $validAnswers); // mostrar las correctas
        }
    }


    public function nextStep()
    {
// Verificamos que todavía haya más preguntas
        if ($this->currentIndex < count($this->questions) - 1) {

            // 🚀 Emitimos el evento para la animación del slide
            $this->dispatch('next-step');
            // Avanzar al siguiente índice
            $this->currentIndex++;



            /***   OMITIMOS SUBSCRITION POR AHORA  ****
            if ($this->currentIndex  == 1) {

            if (!$this->hasSubscription) {
            $this->dispatch('subscription-required');
            return;
            }
            }
             */



            // Actualizar la pregunta actual
            $this->currentQuestion = $this->questions[$this->currentIndex];

            // Limpiar mensajes previos y respuestas
            $this->message = '';
            $this->image = null;
            $this->answered = false;
            $this->selectedAnswer = '';
            $this->userInput = '';


            // 🚀 Emitir evento SOLO si la pregunta tiene video principal
            if (!empty($this->currentQuestion['video'])) {
                $this->dispatch('quiz-video-update', publicId: $this->currentQuestion['video']);
            } else {
                // 👉 Si es tipo "video-choice", dejamos que el JS los inicialice normalmente
                $this->dispatch('quiz-video-refresh');
            }
        } else {
            // Si no hay más preguntas, marcar como completado
            $this->completed();

        }
    }

    public function completed()
    {
        // Calcular el puntaje máximo posible
        $total = count($this->questions) * 10;
        $percentage = ($this->score / $total) * 100;

        // Si el puntaje es menor a 80, no guardar en la base de datos
        if ($percentage < 80) {
            $this->dispatch('quiz-failed', percentage: $percentage);
            return; // Muy importante: detener ejecución
        }

        // ✅ Evitar guardar si ya se completó antes
        if (SecureStorage::get('data.token')) {
            $token = SecureStorage::get('data.token');
            $userId = SecureStorage::get('data.user.id');


            // Verificar si ya existe un registro de ese quiz
            $checkUrl = sprintf(
                '%s/v1/quiz-results/check/%s/%s/%s/%s',
                config('services.api.url'),
                session('data.user.id'),
                $this->slug .'-themes',
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
                    // ✅ Ya existe → no guardar otra vez
                    $this->dispatch('quiz-finished');
                    return;
                }
            }

            // Si no existe, guardar el resultado
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($token)
                ->acceptJson()
                ->post(config('services.api.url') . '/v1/quiz-results', [
                    'user_id'   => $userId,
                    'syllabus'  => $this->slug .'-themes',
                    'theme'     => $this->slug_theme,
                    'type'      => $this->type,
                    'score'     => $this->score,
                    'played_at' => now()->toDateString(),
                ]);

            if ($response->failed()) {
                $this->message = "⚠️ Error al guardar el resultado.";
            } else {
                $this->message = '';
            }
        }

        $this->dispatch('quiz-finished');
    }


    public function getCanValidateProperty(): bool
    {
        $current = $this->questions[$this->currentIndex] ?? null;

        if (!$current) {
            return false;
        }

        // Si la pregunta es tipo texto → validar que el usuario escribió algo
        if ($current['type'] === 'text') {
            return !empty(trim($this->userAnswer));
        }

        // Para las demás (choice, video-choice, yes-no) → validar que haya una selección
        return !empty($this->selectedAnswer);
    }


    private function normalizeAnswer($text)
    {
        $text = strtolower(trim($text));

        // Reemplazar caracteres acentuados por sus equivalentes sin acento
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



    public function render()
    {


//        if ($this->completed) {
//
//            return view('livewire.videos.completed', [
//                'score' => $this->score,
//                'total' => count($this->questions) * 10,
//                'slug' => $this->slug
//            ]);
//        }

        $currentQuestion = $this->questions[$this->currentIndex] ?? null;







        return view('livewire.sign-video-quiz', [
            'currentQuestion' => $currentQuestion,
            'score' => $this->score
        ]);
    }
}
