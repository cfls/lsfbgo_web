<div class="flex flex-col min-h-screen">

    {{-- Header con Gradient --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-4 py-3">
            <div class="flex items-center gap-2">
                @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                <flux:subheading size="xl" class="text-white">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="flex flex-col flex-1 px-4 py-5 gap-5 mb-5">

        {{-- 🔍 RECHERCHE --}}
        <flux:field>
           
            <flux:input
                wire:model.debounce.300ms="search"
                placeholder="Rechercher un mot…"
                wire:keydown="$set('letter', 'tous')"
            >
                <x-slot name="iconTrailing">
                    @if($search)
                        <flux:button
                            size="sm"
                            variant="subtle"
                            icon="x-mark"
                            class="-mr-1"
                            wire:click="clearSearch"
                        />
                    @else
                        <flux:icon icon="magnifying-glass" class="text-gray-400" />
                    @endif
                </x-slot>
            </flux:input>
        </flux:field>

        {{-- 🔠 TABS A–Z --}}
        @php
            //$letters = array_merge(['tous'], range('A', 'Z'));
             $letters = range('A', 'Z');
        @endphp

        <div
            x-data="{
                isDown: false,
                startX: 0,
                scrollLeft: 0,
                scrollToLetter(el) {
                    const container = this.$refs.az;
                    const rect = el.getBoundingClientRect();
                    const containerRect = container.getBoundingClientRect();
                    const offset = rect.left - containerRect.left - (containerRect.width / 2) + (rect.width / 2);
                    container.scrollTo({ left: container.scrollLeft + offset, behavior: 'smooth' });
                },
                startDrag(e) {
                    this.isDown = true;
                    this.startX = (e.touches ? e.touches[0].pageX : e.pageX);
                    this.scrollLeft = this.$refs.az.scrollLeft;
                },
                stopDrag() { this.isDown = false; },
                moveDrag(e) {
                    if (!this.isDown) return;
                    e.preventDefault();
                    const x = (e.touches ? e.touches[0].pageX : e.pageX);
                    const walk = (x - this.startX) * 1.5;
                    this.$refs.az.scrollLeft = this.scrollLeft - walk;
                }
            }"
            class="mt-5"
        >
            <div
                x-ref="az"
                class="flex gap-2 overflow-x-auto whitespace-nowrap no-scrollbar py-1 cursor-grab active:cursor-grabbing select-none"
                @mousedown="startDrag"
                @mouseleave="stopDrag"
                @mouseup="stopDrag"
                @mousemove="moveDrag"
                @touchstart.passive="startDrag"
                @touchend.passive="stopDrag"
                @touchmove.passive="(e) => moveDrag(e.touches[0])"
            >
                @foreach ($letters as $ltr)
                    <button
                        type="button"
                        wire:click="setLetter('{{ $ltr }}')"
                        x-ref="letter_{{ $ltr }}"
                        @click="setTimeout(() => scrollToLetter($refs['letter_{{ $ltr }}']), 50)"
                        class="px-3 py-1.5 rounded-full text-sm font-medium flex-shrink-0 transition
                            {{ $letter === $ltr
                                ? 'bg-emerald-600 text-white'
                                : 'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-200' }}"
                    >
                        {{ $ltr }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ⏳ SKELETON / LISTA CON SCROLL INFINITO --}}
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
                    });
                }
            }"
            class="flex-1 overflow-y-auto overscroll-contain no-scrollbar mt-5"
        >
            @if ($isLoading)
                <div class="space-y-3 animate-pulse">
                    @for ($i = 0; $i < 8; $i++)
                        <div class="h-14 bg-gray-200 dark:bg-zinc-800 rounded-xl"></div>
                    @endfor
                </div>

            @elseif (count($items) === 0)
                <div class="text-center text-gray-500 py-12">
                    Aucun résultat trouvé
                </div>

            @else
                <div class="space-y-3 pb-6">
                    @foreach ($items as $item)
                        <div
                            wire:key="dict-item-{{ $item['id'] }}"
                            class="flex items-center justify-between w-full px-4 py-4
                                   bg-white border border-gray-200 rounded-xl shadow-sm
                                   dark:bg-zinc-800 dark:border-zinc-700
                                   hover:bg-gray-50 dark:hover:bg-zinc-700
                                   active:scale-[0.98] transition cursor-pointer"
                            wire:click="$dispatch('openVideoModal', { id: {{ $item['id'] }} })"
                        >
                            <span class="font-medium text-base">{{ $item['title'] }}</span>

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 text-gray-400 dark:text-zinc-400 flex-shrink-0"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                    @endforeach

                    @if($hasMorePages)
                        <div x-ref="trigger" class="py-4">
                            @if($isLoadingMore)
                                <div class="flex justify-center">
                                    <div class="animate-spin rounded-full h-7 w-7 border-b-2 border-emerald-600"></div>
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
                </div>
            @endif
        </div>

    </div>

    @livewire('video-modal')
</div>