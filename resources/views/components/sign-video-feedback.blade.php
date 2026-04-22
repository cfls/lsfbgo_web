@props([
    'isCorrect' => false,
    'message' => '',
    'image' => null,
    'userAnswer' => '',
    'currentQuestion' => [],
])

@if($image)
<div
    x-data="{ show: false }"
    x-init="$nextTick(() => { setTimeout(() => { show = true }, 50) })"
    x-on:close-quiz-modals.window="show = false"
    :style="show
        ? 'max-height: 600px; bottom: calc(56px + env(safe-area-inset-bottom, 0px))'
        : 'max-height: 0px; bottom: calc(56px + env(safe-area-inset-bottom, 0px))'"
    class="fixed inset-x-0 z-50 flex flex-col items-center gap-3 px-6 pt-5 pb-5 rounded-t-3xl shadow-[0_-4px_24px_rgba(0,0,0,0.15)] overflow-hidden transition-[max-height] duration-500 ease-out {{ $isCorrect ? 'bg-emerald-500 dark:bg-emerald-800' : 'bg-red-500 dark:bg-red-800' }}"
>
    {{-- Imagen --}}
    <img
        src="{{ $isCorrect ? asset('/img/lsfbgo/good.png') : asset('/img/lsfbgo/bad.png') }}"
        alt="{{ $isCorrect ? 'bon' : 'mal' }}"
        class="w-20 h-20 object-contain flex-shrink-0"
    />

    {{-- Texto --}}
    <div class="flex flex-col items-center gap-2 text-center w-full">
        @if($isCorrect)
            <span class="text-white font-bold text-xl leading-snug">
                Bravo ! C'est la bonne réponse.
            </span>
        @else
            <span class="text-white font-bold text-xl leading-snug">Dommage !</span>
            @if(($currentQuestion['type'] ?? null) !== 'video-choice')
                <div class="flex flex-col items-center gap-2 w-full">
                    {{-- Lo que escribió el usuario --}}
                    <div class="flex items-center gap-2 bg-white/20 rounded-xl px-4 py-2 w-full justify-center">
                        <span class="text-white/70 text-xs uppercase tracking-widest">Tu as écrit</span>
                        <span class="text-white font-bold text-lg line-through opacity-70">{{ $userAnswer }}</span>
                    </div>
                    {{-- Flecha --}}
                    <span class="text-white/60 text-base leading-none">↓</span>
                    {{-- Respuesta correcta --}}
                    <div class="flex items-center gap-2 bg-white/30 rounded-xl px-4 py-2 w-full justify-center">
                        <span class="text-white/70 text-xs uppercase tracking-widest">Réponse</span>
                        <span class="text-white font-extrabold text-2xl">{{ $message }}</span>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Botón siguiente --}}
    <div class="w-full flex justify-center mt-1">
        <x-sign-video-next-button :nextStep="'nextStep'" />
    </div>
</div>
@endif