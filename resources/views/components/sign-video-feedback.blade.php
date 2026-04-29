@props([
    'isCorrect' => false,
    'message' => '',
    'image' => null,
    'userAnswer' => '',
    'currentQuestion' => [],
    'isLastQuestion' => false,
])

@if($image)
    <div x-data="{ show: false }"
         x-init="$nextTick(() => { setTimeout(() => { show = true }, 50) })"
         x-show="!openFeedback"
         x-on:close-quiz-modals.window="show = false"
         :style="show ? 'max-height: 600px; bottom: 0px' : 'max-height: 0px; bottom: 0px'"
         class="fixed left-0 right-0 z-50 flex flex-col items-center gap-3 px-6 pt-5 pb-5 rounded-t-3xl shadow-[0_-4px_24px_rgba(0,0,0,0.15)] overflow-hidden transition-[max-height] duration-500 ease-out {{ $isCorrect ? 'bg-green-500 dark:bg-green-800' : 'bg-red-500 dark:bg-red-800' }}">

        <img src="{{ $isCorrect ? asset('img/lsfbgo/good.png') : asset('img/lsfbgo/bad.png') }}"
             alt="{{ $isCorrect ? 'bon' : 'mal' }}"
             class="w-20 h-20 object-contain flex-shrink-0">

        <div class="flex flex-col items-center gap-2 text-center w-full">

            <span class="text-white font-bold text-xl leading-snug">
                {{ $isCorrect ? 'Bravo !' : 'Dommage !' }}
            </span>

            {{-- ✅ Aviso de acento/typo cuando es correcto --}}
            @if($isCorrect && $message)
                <div class="flex items-center gap-2 bg-white/30 rounded-xl px-4 py-2 w-full justify-center">
                    <span class="text-white font-extrabold text-lg">{{ $message }}</span>
                </div>
            @endif

            @if(!$isCorrect)
                <div class="flex flex-col items-center gap-2 w-full">
{{--                    <div class="flex items-center gap-2 bg-white/20 rounded-xl px-4 py-2 w-full justify-center">--}}
{{--                        <span class="text-white/70 text-xs uppercase tracking-widest">Tu as écrit</span>--}}
{{--                        <span class="text-white font-bold text-lg line-through opacity-70">{{ $userAnswer }}</span>--}}
{{--                    </div>--}}

{{--                    <span class="text-white/60 text-base leading-none">↓</span>--}}

                    <div class="flex items-center gap-2 bg-white/30 rounded-xl px-4 py-2 w-full justify-center">
                        <span class="text-white/70 text-xs uppercase tracking-widest">Réponse</span>
                        <span class="text-white font-extrabold text-2xl">{{ $message }}</span>
                    </div>
                </div>
{{--            @else--}}
{{--                <div class="flex items-center gap-2 bg-white/30 rounded-xl px-4 py-2 w-full justify-center">--}}
{{--                    <span class="text-white font-bold text-lg">{{ $message ?: $currentQuestion['answer'] ?? '' }}</span>--}}
{{--                </div>--}}
            @endif
        </div>

        <div class="w-full flex justify-center mt-1">
            <div class="flex justify-center mt-4 mb-4 px-6 w-full max-w-sm">
                <button type="button"
                        class="relative items-center font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg ps-4 pe-4 inline-flex w-full max-w-sm
                           [--color-accent:var(--color-blue-600)] [--color-accent-foreground:var(--color-white)]
                           bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)]
                           text-[var(--color-accent-foreground)] border border-black/10 shadow-[inset_0px_1px_--theme(--color-white/.2)]
                           dark:[--color-accent:var(--color-blue-500)] dark:[--color-accent-foreground:var(--color-white)]"
                        @click.prevent="goNext()"
                        :disabled="isTransitioning">
                    <span>{{ $isLastQuestion ? 'Terminer ✓' : 'Suivant →' }}</span>
                </button>
            </div>
        </div>
    </div>
@endif