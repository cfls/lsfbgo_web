@props([
    'isCorrect' => false,
    'message' => '',
    'image' => null,
    'currentQuestion' => [],
])

@if($image)
    <div
        x-data="{ show: false }"
        x-init="$nextTick(() => { setTimeout(() => { show = true }, 50) })"
        x-on:close-quiz-modals.window="show = false" 
        :style="show
            ? 'max-height: 400px; bottom: calc(56px + env(safe-area-inset-bottom, 0px))'
            : 'max-height: 0px; bottom: calc(56px + env(safe-area-inset-bottom, 0px))'"
        class="fixed inset-x-0 flex flex-col items-center gap-2 pb-6 pt-4 rounded-t-3xl shadow-[0_-4px_16px_rgba(0,0,0,0.1)] overflow-hidden transition-[max-height] duration-500 ease-out {{ $isCorrect ? 'bg-green-500 dark:bg-green-900' : 'bg-red-500 dark:bg-red-900' }}"
    >
        {!! $image !!}

        @if($isCorrect)
            <span class="text-white font-bold text-xl">
                Bravo ! C'est la bonne réponse.
            </span>
        @else
            <span class="text-white font-bold text-xl">Dommage !</span>
            @if(($currentQuestion['type'] ?? null) !== 'video-choice')
                <span class="text-white text-base">
                    La bonne réponse est :
                    <span class="font-semibold text-white ">{{ $message }}</span>
                </span>
            @endif
        @endif
        <x-sign-video-next-button :nextStep="'nextStep'" />
    </div>
@endif