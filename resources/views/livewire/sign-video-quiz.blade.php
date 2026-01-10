<div class="space-y-4 min-h-screen">
    {{-- Header Section --}}
    @include('partials.quiz.header', ['slug' => $slug])

    {{-- Main Quiz Container --}}
    <div class="rounded-xl w-full mx-auto" x-data="quizData()">

        {{-- Modals --}}
        @include('partials.quiz.modals.success')
        @include('partials.quiz.modals.failure')
        @include('partials.quiz.modals.subscription')

        {{-- Quiz Content --}}
        <div class="bg-gray-300 p-5">
            @if($currentQuestion)
                <div x-transition:enter="transform transition ease-out duration-700"
                     x-transition:enter-start="translate-x-20 opacity-0 scale-95"
                     x-transition:enter-end="translate-x-0 opacity-100 scale-100"
                     x-transition:leave="transform transition ease-in duration-500"
                     x-transition:leave-start="-translate-x-20 opacity-0 scale-95"
                     x-transition:leave-end="translate-x-20 opacity-0 scale-95"
                     :class="{ 'translate-x-full opacity-0': slideOut }">

                    {{-- Question Header --}}
                    @include('partials.quiz.question-header')


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
    <script src="{{ asset('quiz/alpine-data.js') }}"></script>
    <script src="{{ asset('quiz/cloudinary-player.js') }}"></script>
@endpush