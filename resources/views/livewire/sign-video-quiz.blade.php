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
                    @include('partials.quiz.video-display')


                    {{-- Question Type Components --}}
                    @switch($currentQuestion['type'])

                        @case('choice')
                            <x-sign-video-validate-button
                                    :options="$currentQuestion['options']"
                                    :selectedAnswer="$selectedAnswer"
                                    :answered="$answered"
                            />
                            @break

                        @case('text')
                            <div class="flex justify-center mt-4">
                                <input type="text"
                                       wire:model.live="userInput"
                                       class="border rounded px-3 py-2 text-center bg-white w-1/2 text-black"
                                       placeholder="Votre réponse" />
                            </div>
                            @break

                        @case('match')
                            @php
                                $pairs = is_string($currentQuestion['options'])
                                    ? json_decode($currentQuestion['options'], true)
                                    : $currentQuestion['options'];
                            @endphp

                            @livewire('sign-video-match', [
                                'pairs' => $pairs,
                            ], key('sign-video-match-'.$currentIndex))
                            @break

                        @case('video-choice')
                            <div x-show="!slideOut"
                                 x-transition:enter="transition-all ease-out duration-500"
                                 x-transition:enter-start="opacity-0 translate-y-3"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition-all ease-in duration-300"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-3"
                                 wire:key="video-options-{{ $currentIndex }}">

                                <x-sign-video-options
                                        :options="$currentQuestion['options']"
                                        :correct="$currentQuestion['answer']"
                                        :selected="$selectedAnswer"
                                        :answered="$answered"
                                />
                            </div>
                            @break

                        @case('yes-no')
                            <div class="flex gap-4 justify-center mt-4">
                                <button type="button"
                                        class="px-5 py-2 rounded-lg border transition-colors
                                               {{ $answered ? 'pointer-events-none opacity-70' : '' }}
                                               {{ $selectedAnswer === 'oui' ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
                                        wire:click="selectAnswer('oui')">
                                    Oui
                                </button>

                                <button type="button"
                                        class="px-5 py-2 rounded-lg border transition-colors
                                               {{ $answered ? 'pointer-events-none opacity-70' : '' }}
                                               {{ $selectedAnswer === 'non' ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
                                        wire:click="selectAnswer('non')">
                                    Non
                                </button>
                            </div>
                            @break

                        @default
                            <div class="text-center text-red-500 p-4">
                                Type de question non reconnu: {{ $currentQuestion['type'] }}
                            </div>
                    @endswitch

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

@push('scripts-video-quiz')
    <script src="{{ asset('quiz/cloudinary-player.js') }}"></script>
@endpush