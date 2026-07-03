<div class="space-y-4">
    @php
        $videos = collect(data_get($results, 'attributes.videos', []));
        $annexes = collect(data_get($results, 'attributes.annexes', []));

        $slugSyllabu = data_get($results, 'attributes.slug_syllabu');
        $slugTheme = data_get($results, 'attributes.slug');

        $principalVideos = $videos
            ->where('type', 'principal')
            ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->map(fn ($video) => [
                'id' => $video['id'],
                'title' => $video['title'],
                'url' => route('syllabus.theme', [
                    $slugSyllabu,
                    $slugTheme,
                    'id' => $video['id'],
                ]),
            ]);

        $plusVideos = $annexes
            ->where('type', 'pour_en_savoir_plus')
            ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->map(fn ($video) => [
                'id' => $video['id'],
                'title' => $video['title'],
                'url' => route('syllabus.theme', [
                    $slugSyllabu,
                    $slugTheme,
                    'id' => $video['id'],
                ]),
            ]);
    @endphp
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
      <div class="flex-1 overflow-y-auto px-3 py-6 scroll-touch no-scrollbar" x-data="videoSearch('principal')">
          <flux:heading level="2" class="mb-4">
              <flux:text class="text-center uppercase text-2xl" variant="strong">
                  @if($annexes->isEmpty())
                  {{ data_get($results, 'attributes.title') }}
                  @endif
              </flux:text>
          </flux:heading>


          @if($annexes->isNotEmpty())
              <div class="grid grid-cols-2 gap-2 mt-5 space-x-2">
                  <button
                          type="button"
                          @click="section = 'principal'"
                          :class="section === 'principal'
                        ? 'bg-blue-600 text-white shadow-lg scale-105'
                        : 'bg-white text-gray-700 dark:bg-gray-700 dark:text-gray-200 border border-gray-300'"
                          class="rounded-lg px-4 py-3 font-bold transition"
                  >
                      {{ data_get($results, 'attributes.title') }}
                  </button>

                  <button
                          type="button"
                          @click="section = 'plus'"
                          :class="section === 'plus'
                        ? 'bg-blue-600 text-white shadow-lg scale-105'
                        : 'bg-white text-gray-700 dark:bg-gray-700 dark:text-gray-200 border border-gray-300'"
                          class="rounded-lg px-4 py-3 font-bold transition"
                  >
                      Pour en savoir plus
                  </button>
              </div>
          @endif


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


            <ul
                    id="video-list"
                    role="list"
                    class="space-y-2 mt-4 max-h-[60vh] overflow-y-auto pr-2 scroll-smooth no-scrollbar list-none p-0 m-0"
            >
                <template x-for="video in filteredVideos" :key="video.id">
                    <li
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="rounded-lg font-bold hover:bg-orange-700 transition"
                            style="background-color: {{ $color }};"
                    >
                        <a
                                :href="video.url"
                                :aria-label="video.title"
                                class="flex px-4 py-5"
                        >
                <span class="flex font-bold items-center justify-between text-white w-full">
                    <span x-text="video.title"></span>
                    <svg class="size-6 text-white" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                        </a>
                    </li>
                </template>
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
                section: localStorage.getItem('theme_section') ?? 'principal',
                search: '',

                principalVideos: @json($principalVideos),
                plusVideos: @json($plusVideos),

                get currentVideos() {
                    return this.section === 'plus'
                        ? this.plusVideos
                        : this.principalVideos;
                },

                get filteredVideos() {
                    if (this.search.trim() === '') {
                        return this.currentVideos;
                    }

                    const searchLower = this.search.toLowerCase();

                    return this.currentVideos.filter(video =>
                        video.title.toLowerCase().includes(searchLower)
                    );
                },

                get visibleCount() {
                    return this.filteredVideos.length;
                },

                init() {
                    if (this.section === 'plus' && this.plusVideos.length === 0) {
                        this.section = 'principal';
                    }

                    this.$watch('section', (value) => {
                        localStorage.setItem('theme_section', value);
                        this.resetSearch();
                    });
                },

                resetSearch() {
                    this.search = '';
                }
            };
        }
    </script>
@endpush`