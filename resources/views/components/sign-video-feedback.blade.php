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
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-40 flex flex-col items-center gap-3 px-6 pt-5 pb-[calc(1.25rem+env(safe-area-inset-bottom))] rounded-t-3xl shadow-[0_-4px_24px_rgba(0,0,0,0.15)] {{ $isCorrect ? 'bg-green-500 dark:bg-green-800' : 'bg-red-500 dark:bg-red-800' }}">

        <img src="{{ $isCorrect ? asset('img/lsfbgo/good.png') : asset('img/lsfbgo/bad.png') }}"
             alt="{{ $isCorrect ? 'bon' : 'mal' }}"
             class="w-20 h-20 object-contain flex-shrink-0">

        <div class="flex flex-col items-center gap-2 text-center w-full">
            <span class="text-white font-bold text-xl leading-snug">
                {{ $isCorrect ? 'Bravo !' : 'Dommage !' }}
            </span>

            @if($isCorrect && $message)
                <div class="flex items-center gap-2 bg-white/30 rounded-xl px-4 py-2 w-full justify-center">
                    <span class="text-white font-extrabold text-lg">{{ $message }}</span>
                </div>
            @endif

            @if(!$isCorrect)
                <div class="flex flex-col items-center gap-2 w-full">
                    <div class="flex items-center gap-2 bg-white/30 rounded-xl px-4 py-2 w-full justify-center">
                        <span class="text-white/70 text-xs uppercase tracking-widest">Réponse</span>
                        <span class="text-white font-extrabold text-2xl">{{ $message }}</span>
                    </div>
                </div>
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
                        @click.prevent="goNext()">
                    <span>{{ $isLastQuestion ? 'Terminer ✓' : 'Suivant →' }}</span>
                </button>
            </div>
        </div>
    </div>
@endif