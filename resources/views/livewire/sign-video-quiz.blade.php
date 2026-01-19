<div class="space-y-4 min-h-screen">
    {{-- Header Section --}}
{{--    @include('partials.quiz.header', ['slug' => $slug])--}}

    {{-- Main Quiz Container --}}
    <div class="rounded-xl w-full mx-auto" x-data="quizData()">

        {{-- Modals --}}
        @include('partials.quiz.modals.success')
        @include('partials.quiz.modals.failure')


        {{-- Quiz Content --}}
        <div class=" p-5">
            {{-- Question Header --}}
            @include('partials.quiz.question-header')
            @if($currentQuestion)
                <div x-show="!isTransitioning"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"

                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-90">



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
                        this.score = event.detail.percentage;
                    });

                    window.addEventListener('quiz-finished', () => {
                        this.showFailModal = false;
                        this.openCongrats = true;
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