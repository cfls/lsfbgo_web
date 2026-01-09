@props([
    'options' => [],
    'correct' => null,
    'selected' => null,
    'answered' => false,
])

<div id="videochoice" class="w-full flex flex-wrap justify-center gap-4 mt-4">
    @foreach($options as $index => $option)
        @php
            $value = $option['value'] ?? '';
            $video = $option['video'] ?? '';
        @endphp

        <div
                class="relative w-[180px] sm:w-[200px] transition-transform duration-300"
                wire:ignore
                x-data="{
                value: @js($value),
                correct: @js($correct),
                selected: @entangle('selectedAnswer'),
                answered: @entangle('answered'),
            }"
                @click.stop="if (!answered) { selected = value; $wire.selectAnswer(value) }"
        >
            <video
                    id="option-{{ $index }}"
                    class="cld-video-player cld-option cld-fluid w-full h-auto rounded-lg pointer-events-none"
                    data-public-id="{{ $video }}"
                    muted autoplay loop playsinline
            ></video>

            <div class="absolute inset-0 rounded-xl  transition-all duration-300 pointer-events-none"
                 :class="{
                    'border-green-500 ring-4 ring-green-300 scale-105 shadow-lg': answered && value === correct,
                    'border-red-600 ring-4 ring-red-300 scale-105 shadow-lg': answered && selected === value && value !== correct,
                    'border-blue-500 ring-4 ring-blue-300 scale-105 shadow-lg': !answered && selected === value,
                    'border-gray-300 scale-100 shadow-none': !answered && selected !== value,
                    'opacity-60': answered
                 }">
            </div>

        </div>
    @endforeach
</div>
