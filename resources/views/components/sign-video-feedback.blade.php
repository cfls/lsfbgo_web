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
            ? 'max-height: 100px; bottom: calc(56px + env(safe-area-inset-bottom, 0px))'
            : 'max-height: 0px; bottom: calc(56px + env(safe-area-inset-bottom, 0px))'"
        class="fixed inset-x-0 flex flex-row items-center gap-3 px-4 py-3 shadow-[0_-4px_16px_rgba(0,0,0,0.1)] overflow-hidden transition-[max-height] duration-500 ease-out {{ $isCorrect ? 'bg-green-500 dark:bg-green-900' : 'bg-red-500 dark:bg-red-900' }}"
    >
        {{-- Emoji en lugar de imagen grande --}}
       <img 
            src="{{ $isCorrect ? asset('/img/lsfgo/good.png') : asset('/img/lsfgo/bad.png') }}" 
            alt="{{ $isCorrect ? 'bon' : 'mal' }}"
            class="w-14 h-14 object-contain flex-shrink-0 rounded-full dark:bg-gray-200"
        />

        {{-- Texto --}}
        <div class="flex-1 flex flex-col justify-center min-w-0">
            @if($isCorrect)
                <span class="text-white font-bold text-sm leading-snug">
                    Bravo ! C'est la bonne réponse.
                </span>
            @else
                <span class="text-white font-bold text-sm leading-snug">Dommage !</span>
                @if(($currentQuestion['type'] ?? null) !== 'video-choice')
                    <span class="text-white text-xs leading-snug">
                        Réponse : <span class="font-semibold">{{ $message }}</span>
                    </span>
                @endif
            @endif
        </div>

        {{-- Botón siguiente --}}
        <div class="flex-shrink-0">
            <x-sign-video-next-button :nextStep="'nextStep'" />
        </div>
    </div>
@endif