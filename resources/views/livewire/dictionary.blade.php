<div class="flex flex-col h-screen md:h-auto md:min-h-screen md:max-w-2xl md:mx-auto md:my-8 md:rounded-2xl md:shadow-xl overflow-hidden">

    <div class="bg-gradient-to-br from-teal-600 to-purple-600 text-white pt-[var(--inset-top)] md:pt-0 rounded-none md:rounded-t-2xl border-none">
        <div class="px-4 py-3 md:px-6 md:py-5">
            <div class="flex items-center gap-3">
                @include('partials.quiz.svg.logo', [
                    'class' => 'w-14 h-14 md:w-20 md:h-20',
                    'aria-hidden' => 'true'
                ])
                <flux:subheading size="xl" class="text-white text-lg md:text-2xl font-semibold">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="flex flex-col flex-1 min-h-0 px-4 md:px-6 py-5 gap-5 mb-5 md:mb-0">

        {{-- Búsqueda --}}
        <flux:field>
            <flux:label class="sr-only" for="dict-search">
                Rechercher un mot dans le dictionnaire
            </flux:label>
            <flux:input
                    id="dict-search"
                    wire:model.debounce.300ms="search"
                    placeholder="Rechercher un mot…"
                    aria-label="Rechercher un mot dans le dictionnaire"
                    wire:keydown="$set('letter', 'tous')"
                    class="w-full"
            >
                <x-slot name="iconTrailing">
                    @if($search)
                        <flux:button
                                size="sm"
                                variant="subtle"
                                icon="x-mark"
                                class="-mr-1"
                                wire:click="clearSearch"
                                aria-label="Effacer la recherche"
                        />
                    @else
                        <flux:icon icon="magnifying-glass" class="text-gray-400" aria-hidden="true" />
                    @endif
                </x-slot>
            </flux:input>
        </flux:field>

        {{-- Anuncio sr-only --}}
        <span class="sr-only" aria-live="polite" aria-atomic="true">
            @if($search || $letter !== 'tous')
                @if(count($items) === 0)
                    Aucun résultat trouvé
                @else
                    {{ count($items) }} résultat(s) trouvé(s)
                @endif
            @endif
        </span>

        @php $letters = range('A', 'Z'); @endphp

        {{-- Filtro A–Z: scroll en móvil, wrap en desktop --}}
        <div>
            <div
                    role="group"
                    aria-label="Filtrer par lettre"
                    x-ref="az"
                    class="flex gap-2 overflow-x-auto md:overflow-x-visible md:flex-wrap whitespace-nowrap md:whitespace-normal no-scrollbar py-1 cursor-grab active:cursor-grabbing select-none md:cursor-auto"
            >
                @foreach ($letters as $ltr)
                    <button
                            type="button"
                            wire:click="setLetter('{{ $ltr }}')"
                            aria-pressed="{{ $letter === $ltr ? 'true' : 'false' }}"
                            class="px-3 py-1.5 rounded-full text-sm font-medium flex-shrink-0 md:flex-shrink transition
                            {{ $letter === $ltr
                                ? 'bg-emerald-600 text-white'
                                : 'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-zinc-700' }}"
                    >
                        {{ $ltr }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Lista de resultados --}}
        <div
                x-data="{
                init() {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !@json($isLoading) && @json($hasMorePages)) {
                                @this.loadMore();
                            }
                        });
                    }, { threshold: 0.1 });

                    this.$nextTick(() => {
                        const trigger = this.$refs.trigger;
                        if (trigger) observer.observe(trigger);

                        const az = document.querySelector('[x-ref=\'az\']');
                        if (az) {
                            az.querySelectorAll('button').forEach(btn => {
                                btn.addEventListener('focus', () => {
                                    btn.scrollIntoView({ inline: 'nearest', behavior: 'smooth', block: 'nearest' });
                                });
                            });
                        }
                    });
                }
            }"
                class="flex-1 min-h-0 overflow-y-auto md:overflow-y-visible overscroll-contain no-scrollbar"
        >
            <div
                    aria-live="polite"
                    aria-busy="{{ $isLoading ? 'true' : 'false' }}"
            >
                @if ($isLoading)
                    <span class="sr-only">Chargement en cours…</span>
                    <div class="space-y-3 animate-pulse" aria-hidden="true">
                        @for ($i = 0; $i < 8; $i++)
                            <div class="h-14 bg-gray-200 dark:bg-zinc-800 rounded-xl"></div>
                        @endfor
                    </div>

                @elseif (count($items) === 0)
                    <div class="text-center text-gray-500 py-12">
                        Aucun résultat trouvé
                    </div>

                @else
                    {{-- Grid en desktop, lista en móvil --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pb-6">
                        @foreach ($items as $item)
                            <button
                                    type="button"
                                    wire:key="dict-item-{{ $item['id'] }}"
                                    aria-label="Voir la vidéo : {{ $item['title'] }}"
                                    class="flex items-center justify-between w-full text-left px-4 py-4
                                       bg-white text-black dark:text-white border border-gray-200 rounded-xl shadow-sm
                                       dark:bg-zinc-800 dark:border-zinc-700
                                       hover:bg-gray-50 dark:hover:bg-zinc-700
                                       active:scale-[0.98] transition"
                                    wire:click="$dispatch('openVideoModal', { id: {{ $item['id'] }} })"
                            >
                                <span class="font-medium text-base truncate">{{ $item['title'] }}</span>

                                <svg
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5 text-gray-400 dark:text-zinc-400 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </button>
                        @endforeach
                    </div>

                    @if($hasMorePages)
                        <div x-ref="trigger" class="py-4">
                            @if($isLoadingMore)
                                <div class="flex justify-center items-center gap-2">
                                    <div
                                            role="status"
                                            aria-label="Chargement de plus de résultats"
                                            class="animate-spin rounded-full h-7 w-7 border-b-2 border-emerald-600"
                                    ></div>
                                    <span class="sr-only">Chargement de plus de résultats…</span>
                                </div>
                            @else
                                <div class="h-1"></div>
                            @endif
                        </div>
                    @else
                        <div class="text-center text-gray-400 py-4 text-sm">
                            Fin de la liste
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>

    @livewire('video-modal')
</div>