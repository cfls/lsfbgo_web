<div class="space-y-4 min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                <a wire:navigate href="{{ route('games', ['ue' => $this->ue]) }}" class="text-white inline-flex items-center gap-2">
                    <flux:icon.arrow-left-circle class="size-8"/>
                    @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                </a>
                <flux:subheading size="xl" class="text-white">{{ $title }}</flux:subheading>
            </div>
        </div>
    </div>

    {{-- Lista de temas --}}
    <div class="bg-gray-300 p-5 w-full rounded-lg space-y-5 flex flex-col items-center justify-start
                max-h-[85vh] md:max-h-[65vh] overflow-y-auto no-scrollbar">

        @forelse($cards as $card)
            <div class="w-full max-w-sm flex flex-col items-end gap-2">
                <a href="{{ $card['link'] }}" class="w-full">
                    <div class="flex items-center justify-between p-5 border border-gray-200 rounded-lg shadow-sm transition duration-300"
                         style="background-color: {{ $card['colorHex'] }}; color: {{ $card['textColor'] }};">
                        <flux:label class="text-lg font-semibold" style="color: {{ $card['textColor'] }};">
                            @if($card['type'] === 'pour_en_savoir_plus')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                                </svg>
                                {{ $card['iteration'] }}. Pour en savoir plus
                            @else
                                {{ $card['iteration'] }}. {{ $card['title'] }}
                            @endif
                        </flux:label>

                        @if($card['done'])
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="color: {{ $card['textColor'] }};">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="color: {{ $card['textColor'] }};">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        @endif
                    </div>
                </a>
            </div>
        @empty
            <p class="text-gray-600 py-8">No hay temas disponibles.</p>
        @endforelse

            @php
                $totalCards = count($cards);
                $totalDone = collect($cards)->where('done', true)->count();
            @endphp

            @if($totalCards > 0 && $totalCards === $totalDone)
            <div class="w-full max-w-sm flex flex-col items-end gap-2">
                <a
                        wire:navigate
                        href="{{ route('syllabus.syntheses', ['ue' => $this->ue, 'type' => $this->type, 'syntheses' => 'syntheses']) }}"
                        class="flex items-center justify-between w-full px-5 py-4 rounded-xl shadow-sm active:scale-[0.98] hover:brightness-95 hover:shadow-md bg-gradient-to-r from-teal-500 to-purple-600"
                >
                        <span class="flex flex-col items-start">
                            <span class="text-white text-base md:text-lg font-semibold">
                                Synthèse
                            </span>
                            <span class="inline-flex items-center gap-1.5 bg-white/20 rounded-full px-3 py-0.5 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 shrink-0"></span>
                                <span class="text-white text-xs font-medium">Tout débloqué !</span>
                            </span>
                        </span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72.4 72.4" class="w-6 h-6 md:w-7 md:h-7 shrink-0" aria-hidden="true">
                        <circle fill="white" cx="36.2" cy="36.2" r="36.2"/>
                        <polygon fill="#7c3aed" points="12.6 28.3 37.8 28.3 37.8 12.6 61.4 36.2 37.8 59.8 37.8 44.1 12.6 44.1 12.6 28.3"/>
                    </svg>
                </a>
            </div>
            @else
                <div class="w-full max-w-sm flex flex-col items-end gap-2">
                    <div
                            class="flex items-center justify-between w-full gap-4 px-5 py-4
               rounded-xl border border-gray-200 dark:border-white/10
               bg-gray-100 dark:bg-gray-800/80
               shadow-sm
               cursor-not-allowed
               transition-colors duration-300"
                    >
        <span class="flex flex-col items-start min-w-0">

            {{-- Título --}}
            <span class="flex items-center gap-2 text-gray-500 dark:text-gray-400
                         text-base md:text-lg font-semibold">
                Synthèse

                {{-- Candado --}}
                <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="size-4 text-gray-400 dark:text-gray-500"
                        aria-hidden="true"
                >
                    <path
                            fill-rule="evenodd"
                            d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25V9A2.25 2.25 0 0 0 4.5 11.25v8.25a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-8.25A2.25 2.25 0 0 0 17.25 9V6.75A5.25 5.25 0 0 0 12 1.5Zm3.75 7.5V6.75a3.75 3.75 0 0 0-7.5 0V9h7.5Z"
                            clip-rule="evenodd"
                    />
                </svg>
            </span>

            {{-- Estado --}}
            <span
                    class="inline-flex items-center gap-2
                       bg-gray-200 dark:bg-white/10
                       border border-gray-300/60 dark:border-white/5
                       rounded-full px-3 py-1 mt-1.5"
            >
                <span
                        class="w-1.5 h-1.5 rounded-full
                           bg-gray-400 dark:bg-gray-500
                           shrink-0"
                ></span>

                <span class="text-gray-600 dark:text-gray-300 text-xs font-medium">
                    Débloquez tous les thèmes pour accéder à la synthèse
                </span>
            </span>
        </span>

                        {{-- Flecha desactivada --}}
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 72.4 72.4"
                                class="w-7 h-7 md:w-8 md:h-8 shrink-0 opacity-70 dark:opacity-50"
                                aria-hidden="true"
                        >
                            <circle
                                    class="fill-gray-200 dark:fill-gray-700"
                                    cx="36.2"
                                    cy="36.2"
                                    r="36.2"
                            />

                            <polygon
                                    class="fill-gray-400 dark:fill-gray-500"
                                    points="12.6 28.3 37.8 28.3 37.8 12.6 61.4 36.2 37.8 59.8 37.8 44.1 12.6 44.1 12.6 28.3"
                            />
                        </svg>
                    </div>
                </div>
          @endif

    </div>
</div>