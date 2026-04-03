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
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
            {{-- ✅ Botón de velocidad (aquí) --}}
                    <div>
                        <a href="{{ route('questions',['ue' => $slug, 'type' => $type]) }}"  aria-label="Quitter le quiz">                           
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            
                        </a>
                </div>
                   <div>
                      <button
                            @click="openFeedback = true"
                            aria-label="Envoyer un commentaire"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-zinc-800 dark:text-gray-200 dark:border-zinc-700 dark:hover:bg-zinc-700 transition"
                    >
                        <svg 
                        aria-hidden="true" 
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                            </svg>
                    </button>
                    </div>                   
                     <div
                        x-show="!openFeedback"
                        x-transition
                    >
                      <button
                            @click="toggleSpeed()"
                            :aria-pressed="slow.toString()"
                            :aria-label="slow ? 'Mode lent activé' : 'Mode normal activé'"
                            class="flex items-center justify-center w-14 h-14 rounded-full bg-gray-600 dark:bg-white text-white shadow-lg"
                        >
                            <span class="text-[10px]" x-html="slow ? '<img src=\'{{ asset('img/lsfgo/slow.png') }}\' alt=\'Activer la vitesse lente\' class=\'w-14 h-14 object-contain\' />' : '<img src=\'{{ asset('img/lsfgo/speed.png') }}\' alt=\'Activer la vitesse normale\' class=\'w-14 h-14 object-contain\' />'"></span>
                        </button>
                    </div>
                    
                </div>
            </div>   
            {{-- ✅ Barra de progreso mejorada --}}
            {{-- Fuera del div principal, al nivel del layout --}}
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium">
                        Question {{ $currentIndex + 1 }} a {{ count($questions) }}
                    </span>

                                  
                    <span  class="text-sm text-black dark:text-white">
                          Points: {{ $score }} / {{ count($questions) * 10 }}
                    </span>
                      <span class="sr-only" aria-live="polite" aria-atomic="true">
                         Points: {{ $score }} / {{ count($questions) * 10 }}
                      </span>
                 </div>

                <div 
                role="progressbar"
                 aria-valuenow="{{ $currentIndex + 1 }}"
                 aria-valuemin="1"
                 aria-valuemax="{{ count($questions) }}"
                 aria-label="Avancement du quiz"
                class="w-full bg-gray-200 rounded-full h-2.5">
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