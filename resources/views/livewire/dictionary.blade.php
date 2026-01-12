<div class="space-y-6">
    <!-- Header with Gradient -->
    <div
            class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none ">
        <div class="px-4">
            <div class="p-2 inline-block">

                @include('partials.quiz.svg.logo')

                <flux:subheading class="text-white text-xl pb-4">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>
    <div class="flex flex-col items-center justify-center py-6">

        {{-- 🔍 RECHERCHE --}}
        <div class="mb-6 w-full max-w-md">
            <flux:field>
                <flux:label>Rechercher un mot</flux:label>
                <flux:input
                        wire:model.debounce.300ms="search"
                        placeholder="Rechercher un mot…"
                        wire:keydown="$set('letter', 'tous')"
                >
                    <x-slot name="iconTrailing">
                        <flux:button
                                size="sm"
                                variant="subtle"
                                icon="x-mark"
                                class="-mr-1"
                                wire:click="$set('search',''); $set('page', 1)"
                        />
                    </x-slot>
                </flux:input>
            </flux:field>
        </div>

        {{-- 🔠 TABS A–Z (FR-BE) --}}
        {{-- 🔠 Barre A–Z avec scroll + drag + auto-center (SC-3) --}}
        @php
            $letters = array_merge(['tous'], range('A', 'Z'));
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
            this.startX = e.pageX;
            this.scrollLeft = this.$refs.az.scrollLeft;
        },
        stopDrag() { this.isDown = false; },
        moveDrag(e) {
            if (!this.isDown) return;
            e.preventDefault();
            const x = e.pageX;
            const walk = (x - this.startX) * 1.5;
            this.$refs.az.scrollLeft = this.scrollLeft - walk;
        }
    }"
                class="w-full max-w-4xl mb-6"
        >
            <div
                    x-ref="az"
                    class="flex gap-2 overflow-x-auto whitespace-nowrap no-scrollbar py-2 px-1 cursor-grab active:cursor-grabbing select-none"
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
                            @click="
                    setTimeout(() => {
                        scrollToLetter($refs['letter_{{ $ltr }}']);
                    }, 50);
                "
                            class="px-3 py-1 rounded flex-shrink-0 transition
                    {{ $letter === $ltr
                        ? 'bg-emerald-600 text-white'
                        : 'bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-gray-200' }}"
                    >
                        {{ $ltr }}
                    </button>
                @endforeach
            </div>
        </div>


        {{-- ⏳ SKELETON OU LISTE --}}
        <div class="w-full max-w-2xl min-h-[200px] max-h-[60vh] overflow-y-auto overscroll-contain scroll-smooth p-6 no-scrollbar">
            @if ($isLoading)
                {{-- Skeleton: 8 lignes --}}
                <div class="space-y-3 animate-pulse">
                    @for ($i = 0; $i < 8; $i++)
                        <div class="h-12 bg-gray-200 dark:bg-zinc-800 rounded-lg"></div>
                    @endfor
                </div>
            @else
                @if (count($items) === 0)
                    <div class="text-center text-gray-500 py-8">
                        Aucun résultat trouvé
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($items as $item)
                            <div
                                    wire:key="dict-item-{{ $item['id'] }}"
                                    class="flex items-center justify-between w-full p-5 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-zinc-800 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 transition cursor-pointer"
                                    wire:click="$dispatch('openVideoModal', { id: {{ $item['id'] }} })"
                            >
                                <span class="font-medium">{{ $item['title'] }}</span>

                                {{-- Icône flèche --}}
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-6 h-6 text-gray-600 dark:text-white"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>


        @livewire('video-modal')
    </div>

</div>
