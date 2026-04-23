<div class="space-y-4">

     <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-4">
            <div class="p-2 inline-flex items-center gap-2">

                {{-- ✅ Enlace separado del logo, con aria-label descriptivo --}}
                <a
                    wire:navigate
                    href="{{ route('syllabus') }}"
                    aria-label="Retour à la liste des thèmes"
                    class="text-white"
                >
                    <flux:icon.arrow-left-circle class="size-8" aria-hidden="true"/>
                </a>

                {{-- ✅ Logo decorativo fuera del enlace, envuelto en span aria-hidden --}}
                <span aria-hidden="true">
                    @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                </span>

            </div>
        </div>
    </div>

    {{-- ✅ Mover x-data al nivel superior --}}
      <div class="flex-1 overflow-y-auto px-3 py-6 scroll-touch no-scrollbar" x-data="videoSearch()">
         <flux:heading level="2">
            <flux:text class="text-center uppercase text-2xl" variant="strong">
                {{ $this->results['attributes']['title'] }}
            </flux:text>
        </flux:heading>

        <!-- 🔍 Campo de búsqueda con contador -->
        <div class="mt-5">
             <flux:field>
                <flux:label class="sr-only" for="video-search">
                    Rechercher un signe dans la liste
                </flux:label>
                <flux:input
                        icon="magnifying-glass"
                        placeholder="Rechercher un signe..."
                        x-model="search"
                        class="w-full mb-4"
                />
             </flux:field>

            {{-- ✅ Región sr-only aria-live que anuncia el estado de búsqueda al lector de pantalla --}}
            <span
                class="sr-only"
                aria-live="polite"
                aria-atomic="true"
                x-text="search !== ''
                    ? (visibleCount === 0
                        ? 'Aucun signe trouvé pour ' + (search.length > 30 ? search.substring(0, 30) + '…' : search)
                        : visibleCount + ' résultat(s) trouvé(s)')
                    : ''"
            ></span>

           {{-- Contador visual (aria-hidden: el sr-only de arriba ya lo cubre) --}}
            <p
                x-show="search !== ''"
                x-transition
                class="text-sm dark:text-white text-gray-600 mb-3"
                aria-hidden="true"
            >
                <span x-text="visibleCount"></span> résultat(s) trouvé(s)
            </p>


            {{-- ✅ <ul role="list"> con id conectado al aria-controls del input --}}
            <ul
                id="video-list"
                role="list"
                class="space-y-2 max-h-[60vh] overflow-y-auto pr-2 scroll-smooth no-scrollbar list-none p-0 m-0"
            >
                @foreach ($this->results['attributes']['videos'] as $index => $video)
                    <li
                        x-show="visibleVideos[{{ $index }}]"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="rounded-lg font-bold hover:bg-orange-700 transition"
                        style="background-color: {{ $this->color }};"
                    >
                        {{-- ✅ aria-label en el enlace con el título del vídeo --}}
                        <a
                            href="{{ route('syllabus.theme', [
                                $this->results['attributes']['slug_syllabu'],
                                $this->results['attributes']['slug'],
                                'id' => $video['id']
                            ]) }}"
                            aria-label="{{ $video['title'] }}"
                            class="flex px-4 py-5"
                        >
                            <flux:text class="flex font-bold items-center justify-between text-white w-full">
                                <span>{{ $video['title'] }}</span>
                                {{-- ✅ Icono decorativo --}}
                                <flux:icon.arrow-right-circle class="size-6 text-white" aria-hidden="true"/>
                            </flux:text>
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- ✅ Mensaje de sin resultados: aria-live cubierto por el sr-only de arriba --}}
            <div
                x-show="search !== '' && visibleCount === 0"
                x-transition
                class="text-center py-8 text-gray-500"
                aria-hidden="true"
            >
                <p>
                    Aucun signe trouvé pour
                    "<span
                        {{-- ✅ Truncar a 30 chars para evitar desbordamiento visual --}}
                        x-text="search.length > 30 ? search.substring(0, 30) + '…' : search"
                        class="font-semibold"
                    ></span>"
                </p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        function videoSearch() {
            return {
                search: '',
                visibleVideos: {},
                visibleCount: 0,
                videos: @json(collect($this->results['attributes']['videos'])->map(fn($v) => strtolower($v['title']))->values()),

                init() {
                    // Inicializar todos como visibles
                    this.videos.forEach((_, index) => {
                        this.visibleVideos[index] = true;
                    });
                    this.visibleCount = this.videos.length;

                    // ✅ Watch para filtrar cuando cambie search
                    this.$watch('search', (value) => {
                        this.filterVideos(value);
                    });
                },

                filterVideos(searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    let count = 0;

                    this.videos.forEach((title, index) => {
                        const isVisible = searchTerm === '' || title.includes(searchLower);
                        this.visibleVideos[index] = isVisible;
                        if (isVisible) count++;
                    });

                    this.visibleCount = count;
                }
            };
        }
    </script>
@endpush