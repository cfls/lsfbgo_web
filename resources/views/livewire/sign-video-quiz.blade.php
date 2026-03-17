<div class="space-y-4 min-h-screen">

    {{-- Main Quiz Container --}}
    <div class="rounded-xl w-full mx-auto" x-data="quizData()">

        {{-- Modals --}}
        @include('partials.quiz.modals.success')
        @include('partials.quiz.modals.failure')
        @include('partials.quiz.modals.feedback')

        {{-- Quiz Content --}}
        <div class="p-5">
            {{-- ✅ Progreso mejorado con botón de feedback --}}

            
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium">
                            Question {{ $currentIndex + 1 }} a {{ count($questions) }}
                        </span>

                        {{-- ✅ Botón de feedback --}}
                        <button
                                @click="openFeedback = true"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-zinc-800 dark:text-gray-200 dark:border-zinc-700 dark:hover:bg-zinc-700 transition"
                                title="Envoyer un commentaire"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <span>Feedback</span>
                        </button>
                    </div>

                    <span class="text-sm text-white">
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

            {{-- Question Header --}}
            @include('partials.quiz.question-header')

            @if($currentQuestion)
                <div class="overflow-hidden relative">
                    <div x-show="!isTransitioning"
                         x-transition:enter="transition ease-out duration-500 transform"
                         x-transition:enter-start="translate-x-full opacity-0"
                         x-transition:enter-end="translate-x-0 opacity-100"
                         x-transition:leave="transition ease-in duration-300 transform"
                         x-transition:leave-start="translate-x-0 opacity-100"
                         x-transition:leave-end="-translate-x-full opacity-0">

                        @if($currentQuestion['video'])
                            <x-video-display
                                    :video="$currentQuestion['video']"
                                    :type="$currentQuestion['type']"
                                    :currentIndex="$currentIndex"
                            />
                        @endif

                        @if($currentQuestion['type'])
                            @include('partials.quiz.question-types.' . $currentQuestion['type'])
                        @endif

                        @include('partials.quiz.action-buttons')
                        @include('partials.quiz.feedback')
                    </div>
                </div>
            @else
                <div class="flex justify-center">Aucun thème disponible</div>
            @endif
        </div>
    </div>

    {{-- 🐛 DEBUG CONSOLE VISUAL (solo en local) --}}
{{--    @if(app()->environment('local'))--}}
{{--        <div x-data="{--}}
{{--            logs: [],--}}
{{--            show: true,--}}
{{--            clearLogs() { this.logs = [] }--}}
{{--        }"--}}
{{--             @debug-log.window="--}}
{{--            logs.unshift($event.detail);--}}
{{--            if(logs.length > 20) logs.pop();--}}
{{--         "--}}
{{--             class="fixed bottom-4 right-4 z-[9999]">--}}

{{--            --}}{{-- Toggle button --}}
{{--            <button @click="show = !show"--}}
{{--                    class="mb-2 bg-gradient-to-r from-red-600 to-pink-600 text-white px-4 py-2 rounded-full shadow-lg font-bold text-sm hover:scale-105 transition">--}}
{{--                <span x-show="!show">🐛 Show</span>--}}
{{--                <span x-show="show">🐛 Hide</span>--}}
{{--            </button>--}}

{{--            --}}{{-- Console panel --}}
{{--            <div x-show="show"--}}
{{--                 x-transition--}}
{{--                 class="w-96 max-h-[70vh] bg-black/95 text-green-400 rounded-lg shadow-2xl overflow-hidden border-2 border-green-500">--}}

{{--                --}}{{-- Header --}}
{{--                <div class="flex items-center justify-between bg-gradient-to-r from-green-600 to-emerald-600 px-3 py-2">--}}
{{--                    <div class="flex items-center gap-2">--}}
{{--                        <span class="text-white font-bold text-sm">🐛 DEBUG</span>--}}
{{--                        <span class="text-xs text-green-200" x-text="logs.length"></span>--}}
{{--                    </div>--}}
{{--                    <button @click="clearLogs()"--}}
{{--                            class="text-white text-xs hover:bg-white/20 px-2 py-1 rounded">--}}
{{--                        Clear--}}
{{--                    </button>--}}
{{--                </div>--}}

{{--                --}}{{-- Logs --}}
{{--                <div class="p-3 overflow-auto max-h-[60vh] text-xs font-mono">--}}
{{--                    <template x-for="(log, index) in logs" :key="index">--}}
{{--                        <div class="mb-2 pb-2 border-b border-gray-800">--}}
{{--                            <div x-html="log" class="break-words"></div>--}}
{{--                        </div>--}}
{{--                    </template>--}}

{{--                    <div x-show="logs.length === 0" class="text-gray-500 text-center py-4">--}}
{{--                        No logs yet...--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endif--}}
{{--</div>--}}

{{--@if(app()->environment('local'))--}}
{{--    --}}{{-- 🔥 Eruda Mobile Console --}}
{{--    <script src="https://cdn.jsdelivr.net/npm/eruda"></script>--}}
{{--    <script>eruda.init();</script>--}}
{{--@endif--}}

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
            console.log(`[${timestamp}] ${message}`, data || '');

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
                openFeedback: false,
                feedbackType: 'bug',
                feedbackMessage: '',
                feedbackSending: false,
                score: 0,
                openSubscription: false,
                isTransitioning: false,
                liveScore: @entangle('score'),
                totalPoints: {{ count($questions) * 10 }},
                failPercentage: 0,

                // ✅ Método para enviar feedback
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

                        debugLog('📦 Payload prepared', payload);
                        debugLog('🚀 Calling Livewire submitFeedback...');

                        const response = await @this.call('submitFeedback', payload);

                        debugLog('✅ Response received', response);

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
                        debugLog('❌ ERROR', {
                            message: error.message,
                            stack: error.stack
                        });

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
                    setTimeout(() => {
                        this.isTransitioning = false;
                    }, 500);
                }
            };
        }
    </script>
    <script src="{{ asset('quiz/cloudinary-player.js') }}"></script>
@endpush