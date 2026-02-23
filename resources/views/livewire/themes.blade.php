<div class="space-y-4">

    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-4">
            <div class="p-2 inline-block">
                <a wire:navigate href="{{ route('syllabus') }}" class="text-white inline-flex items-center gap-2">
                    <flux:icon.arrow-left-circle class="size-5"/>
                    @include('partials.quiz.svg.logo', ['class' => 'w-8 h-8'])
                </a>
            </div>
        </div>
    </div>

    {{-- ✅ Mover x-data al nivel superior --}}
    <div class="flex-1 overflow-y-auto px-3 py-6 scroll-touch no-scrollbar" x-data="videoSearch()">
        <flux:heading>
            <flux:text class="text-center uppercase text-2xl" variant="strong">
                {{ $this->results['attributes']['title'] }}
            </flux:text>
        </flux:heading>

        <!-- 🔍 Campo de búsqueda con contador -->
        <div class="mt-5">
            <flux:input
                    icon="magnifying-glass"
                    placeholder="Rechercher un signe..."
                    x-model="search"
                    class="w-full mb-4"
            />

            {{-- Contador de resultados --}}
            <p x-show="search !== ''" class="text-sm text-gray-600 mb-3" x-transition>
                <span x-text="visibleCount"></span> résultat(s) trouvé(s)
            </p>

            <!-- Lista con scroll -->
            <div class="space-y-2 max-h-[60vh] overflow-y-auto pr-2 scroll-smooth no-scrollbar">
                @foreach($this->results['attributes']['videos'] as $index => $video)
                    <div
                            x-show="visibleVideos[{{ $index }}]"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="bg-orange-400 px-4 py-5 rounded-lg font-bold hover:bg-orange-700 transition"
                    >
                        <a href="{{ route('syllabus.theme', [
                            $this->results['attributes']['slug_syllabu'],
                            $this->results['attributes']['slug'],
                            'id' => $video['id']
                        ]) }}">
                            <flux:text class="flex font-bold items-center justify-between text-black">
                                <span>{{ $video['title'] }}</span>
                                <flux:icon.arrow-right-circle class="size-6 text-black" />
                            </flux:text>
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Mensaje cuando no hay resultados --}}
            <div
                    x-show="search !== '' && visibleCount === 0"
                    x-transition
                    class="text-center py-8 text-gray-500"
            >
                <p>Aucun signe trouvé pour "<span x-text="search" class="font-semibold"></span>"</p>
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