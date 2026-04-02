<div 
     x-data="quizData()"    
     class="space-y-4 min-h-screen">
    <div class="rounded-xl w-full mx-auto">

        {{-- Modals --}}
        @include('partials.quiz.modals.success')
        @include('partials.quiz.modals.failure')
        @include('partials.quiz.modals.feedback')

        @if ($showPaymentModal)
           @include('partials.quiz.modals.code', ['link' => $selectedLink, 'theme' => $theme])
        @endif

        {{-- Quiz Content --}}
        <div class="p-5">
            {{-- ✅ Barra de progreso mejorada --}}
            {{-- Fuera del div principal, al nivel del layout --}}
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium">
                        Question {{ $currentIndex + 1 }} a {{ count($questions) }}
                    </span>

                    <button
                            @click="openFeedback = true"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-zinc-800 dark:text-gray-200 dark:border-zinc-700 dark:hover:bg-zinc-700 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span>Feedback</span>
                    </button>                
                    <span class="text-sm text-black dark:text-white">
                          Points: {{ $score }} / {{ count($questions) * 10 }}
                    </span>
                </div>

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
                        {{-- @include('partials.quiz.action-buttons') --}}

                        {{-- Feedback Message --}}
                          @if (!$showPaymentModal)
                             @include('partials.quiz.feedback')
                         @endif
                    </div>
                </div>
               
                   {{-- ✅ Botón de velocidad (aquí) --}}
                <div
                    x-show="!openFeedback"
                    x-transition
                >
                    <button
                        @click="toggleSpeed()"
                        class="flex items-center justify-center w-20 h-20 rounded-full bg-blue-500 text-white shadow-lg"
                    >
                         <span class="text-[10px]" x-html="slow ? '<img src=\'{{ asset('img/lsfgo/slow.png') }}\' alt=\'lent\' />' : '<img src=\'{{ asset('img/lsfgo/speed.png') }}\' alt=\'Normal\' />'"></span>
                    </button>
                </div>
                
            @else
                <div class="flex justify-center">Aucun thème disponible</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // 🐛 Helper function para debug visual
        window.debugLog = function(message, data = null) {
            const timestamp = new Date().toLocaleTimeString();
            let logMessage = `<span class="text-yellow-400">[${timestamp}]</span> ${message}`;

            if (data !== null) {
                logMessage += `<br><span class="text-blue-400 ml-4">↳ ${JSON.stringify(data, null, 2)}</span>`;
            }

            // Log to browser console
          // console.log(`[${timestamp}] ${message}`, data || '');

            // Dispatch event for visual console
            window.dispatchEvent(new CustomEvent('debug-log', {
                detail: logMessage
            }));
        };
        function quizData() {
            return {
                slow: false,
                openCongrats: false,
                showFailModal: false,
                score: 0,
                openFeedback: false,
                feedbackType: 'bug',
                feedbackMessage: '',
                feedbackSending: false,
                openSubscription: false,
                isTransitioning: false,
                liveScore: @entangle('score'),
                totalPoints: {{ count($questions) * 10 }},
                failPercentage: 0,

                async submitFeedback() {
                    if (!this.feedbackMessage.trim()) {
                        alert('Veuillez écrire un message');
                        return;
                    }

                    this.feedbackSending = true;

                    try {
                        const payload = {
                            type: this.feedbackType,
                            message: this.feedbackMessage,
                            question_id: {{ $currentQuestion['id'] ?? 'null' }},

                        };

                        // debugLog('📦 Payload prepared', payload);
                        // debugLog('🚀 Calling Livewire submitFeedback...');

                        const response = await @this.call('submitFeedback', payload);

                        // debugLog('✅ Response received', response);

                        // Mostrar notificación de éxito
                        this.$dispatch('notify', {
                            type: 'success',
                            message: 'Merci pour votre retour !'
                        });

                        // Limpiar y cerrar
                        this.feedbackMessage = '';
                        this.feedbackType = 'bug';
                        this.openFeedback = false;

                    } catch (error) {
                        // debugLog('❌ ERROR', {
                        //     message: error.message,
                        //     stack: error.stack
                        // });

                        this.$dispatch('notify', {
                            type: 'error',
                            message: 'Erreur lors de l\'envoi'
                        });

                    } finally {
                        this.feedbackSending = false;
                    }
                },

                toggleSpeed() {
                    this.slow = !this.slow;
                    document.querySelectorAll('video').forEach(v => {
                        v.playbackRate = this.slow ? 0.5 : 1;
                    });
                },

                init() {
                    //console.log('Total preguntas tipo cargadas:', {{ count($questions) }});

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