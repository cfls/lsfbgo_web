<div class="space-y-4 min-h-screen">
    <div class="rounded-xl w-full mx-auto" x-data="quizData()">

        {{-- Modals --}}
        @include('partials.quiz.modals.success')
        @include('partials.quiz.modals.failure')

        {{-- Quiz Content --}}
        <div class="p-5">
            {{-- ✅ Barra de progreso mejorada --}}
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium">
                        Question {{ $currentIndex + 1 }} a {{ count($questions) }}
                    </span>
                    <span class="text-sm font-medium">
                        Points : {{ $score }} / {{ count($questions) * 10 }}
                    </span>
                </div>

                {{-- Barra de progreso --}}
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                         style="width: {{ count($questions) > 0 ? (($currentIndex + 1) / count($questions)) * 100 : 0 }}%">
                    </div>
                </div>
            </div>

            @include('partials.quiz.question-header')

            @if($currentQuestion)
                {{-- ✅ Container con overflow hidden para el slide --}}
                <div class="overflow-hidden relative">
                    <div x-show="!isTransitioning"
                         {{-- ✅ Efecto SLIDE desde la derecha --}}
                         x-transition:enter="transition ease-out duration-500 transform"
                         x-transition:enter-start="translate-x-full opacity-0"
                         x-transition:enter-end="translate-x-0 opacity-100"
                         {{-- ✅ Efecto SLIDE hacia la izquierda al salir --}}
                         x-transition:leave="transition ease-in duration-300 transform"
                         x-transition:leave-start="translate-x-0 opacity-100"
                         x-transition:leave-end="-translate-x-full opacity-0">
                        {{-- Video Display --}}
                        <x-video-display
                                :video="$currentQuestion['video']"
                                :type="$currentQuestion['type']"
                                :currentIndex="$currentIndex"
                        />

                        {{-- Question Type Components --}}
                        @include('partials.quiz.question-types.' . $currentQuestion['type'])

                        {{-- Action Buttons --}}
                        @include('partials.quiz.action-buttons')

                        {{-- Feedback Message --}}
                        @include('partials.quiz.feedback')
                    </div>
                </div>
            @else
                <div class="flex justify-center">Aucun thème disponible</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function quizData() {
            return {
                slow: false,
                openCongrats: false,
                showFailModal: false,
                score: 0,
                openSubscription: false,
                isTransitioning: false,
                liveScore: @entangle('score'),
                totalPoints: {{ count($questions) * 10 }},
                failPercentage: 0,

                toggleSpeed() {
                    this.slow = !this.slow;
                    document.querySelectorAll('video').forEach(v => {
                        v.playbackRate = this.slow ? 0.5 : 1;
                    });
                },

                init() {
                    console.log('Total preguntas tipo cargadas:', {{ count($questions) }});

                    this.$watch('openCongrats', value => {
                        if (value) this.showFailModal = false;
                    });

                    window.addEventListener('quiz-failed', (event) => {
                        this.openCongrats = false;
                        this.showFailModal = true;
                        this.failPercentage = event.detail.percentage || 0;
                    });

                    window.addEventListener('quiz-finished', (event) => {
                        this.showFailModal = false;
                        this.openCongrats = true;
                        if (event.detail) {
                            this.liveScore = event.detail.score || this.liveScore;
                            this.totalPoints = event.detail.total || this.totalPoints;
                        }
                    });

                    window.addEventListener('subscription-required', () => {
                        this.openSubscription = true;
                    });

                    window.addEventListener('next-step', () => {
                        this.handleNextStep();
                    });
                },

                handleNextStep() {
                    this.isTransitioning = true;
                    // ✅ Reducido de 500 a 350ms para que sea más rápido
                    setTimeout(() => {
                        this.isTransitioning = false;
                    }, 350);
                }
            };
        }
    </script>
    <script src="{{ asset('quiz/cloudinary-player.js') }}"></script>
@endpush