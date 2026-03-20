<div class="space-y-4">

    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                <a wire:navigate href="{{ route('syllabus.themes', ['ue' => $this->ue, 'theme' => $this->theme]) }}" class="text-white inline-flex items-center gap-2">
                    <flux:icon.arrow-left-circle class="size-8"/>
                    @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                </a>
            </div>
        </div>
    </div>
    <!-- Video con navegación y controles extra -->
    <div class="container mx-auto px-4 py-6">
        @if(count($videos) > 0)
            <div x-data="{
                    videos: @js($videos),
                    current: {{ $currentIndex }},
                    isPaused: true,
                    isSlow: false,
                    repeatCount: 0,
                    isTransitioning: false,
                    videoLoaded: false,          {{-- ← moved here --}}
                    get currentVideo() {
                        return this.videos[this.current] || null;
                    },
                    prev() {
                        if (this.current > 0) {
                            this.isTransitioning = true;
                            this.videoLoaded = false;  {{-- ← reset here too --}}
                            this.current--;
                            setTimeout(() => {
                                this.resetVideo();
                                this.isTransitioning = false;
                            }, 300);
                        }
                    },
                    next() {
                        if (this.current < this.videos.length - 1) {
                            this.isTransitioning = true;
                            this.videoLoaded = false;  {{-- ← reset here too --}}
                            this.current++;
                            setTimeout(() => {
                                this.resetVideo();
                                this.isTransitioning = false;
                            }, 300);
                        }
                    },
                    togglePlay() {
                        const video = this.$refs.player;
                        if (!video) return;
                        if (video.paused) {
                            video.play();
                            this.isPaused = false;
                        } else {
                            video.pause();
                            this.isPaused = true;
                        }
                    },
                    toggleSpeed() {
                        const video = this.$refs.player;
                        if (!video) return;
                        if (this.isSlow) {
                            video.playbackRate = 1;
                            this.isSlow = false;
                        } else {
                            video.playbackRate = 0.5;
                            this.isSlow = true;
                        }
                    },
                    resetVideo() {
                        const video = this.$refs.player;
                        if (!video) return;
                        video.pause();
                        video.currentTime = 0;
                        this.isPaused = true;
                        this.isSlow = false;
                        this.repeatCount = 0;
                        this.videoLoaded = false;  {{-- ← reset here --}}
                        video.playbackRate = 1;
                        video.play().then(() => {
                            this.isPaused = false;
                        }).catch(err => {
                            console.log('Autoplay prevented:', err);
                        });
                        video.onended = () => {
                            if (this.repeatCount < 1) {
                                this.repeatCount++;
                                video.currentTime = 0;
                                video.play();
                            } else {
                                this.isPaused = true;
                            }
                        };
                    }
                }"
                 x-init="$nextTick(() => { if (currentVideo) resetVideo(); })"
                 class="space-y-5 mt-5 text-center"
            >
                <!-- Título del video con transición -->
                <p
                        class="text-lg text-zinc-800 dark:text-white text-center font-semibold transition-all duration-300"
                        x-text="currentVideo?.title"
                        :class="isTransitioning ? 'opacity-0 transform -translate-y-2' : 'opacity-100 transform translate-y-0'"
                ></p>

                <!-- Reproductor de video con transición -->
                <div class="relative w-full max-w-5xl mx-auto aspect-video">

                    {{-- Skeleton --}}
                    <div x-show="!videoLoaded"
                         class="absolute inset-0 bg-gray-900 rounded-lg flex items-center justify-center z-10">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-10 h-10 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-white text-sm">Chargement...</span>
                        </div>
                    </div>

                    <video
                            x-ref="player"
                            class="w-full h-full rounded-lg shadow-lg transition-all duration-500"
                            :class="{
                'opacity-0': !videoLoaded,
                'opacity-100': videoLoaded,
                'scale-95 blur-sm': isTransitioning,
                'scale-100 blur-0': !isTransitioning
            }"
                            :src="currentVideo?.url"
                            autoplay
                            muted
                            playsinline
                            @loadeddata="videoLoaded = true"
                    >
                        Votre navigateur ne supporte pas la vidéo.
                    </video>
                </div>

                <!-- Botones de navegación mejorados -->
                <div class="flex justify-center items-center mt-8 gap-6">
                    <!-- Botón Anterior -->
                    <button
                            @click="prev"
                            class="group relative bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-full disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-110 hover:shadow-2xl active:scale-95 disabled:hover:scale-100 overflow-hidden"
                            :disabled="current === 0"
                    >
                        <div class="flex items-center gap-2 relative z-10">
                            <flux:icon.arrow-left class="size-6 transition-transform duration-300 group-hover:-translate-x-1" />
                            <span class="font-semibold hidden sm:inline">Précédent</span>
                        </div>
                        <!-- Efecto de brillo -->
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                        <!-- Efecto de onda -->
                        <span class="absolute inset-0 rounded-full bg-white opacity-0 group-active:opacity-30 transition-opacity duration-150"></span>
                    </button>

                    <!-- Indicador de progreso -->
                    <div class="flex flex-col items-center px-4 min-w-[120px]">
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <span x-text="current + 1" class="text-blue-600 dark:text-blue-400"></span>
                        <span class="text-gray-400">/</span>
                        <span x-text="videos.length"></span>
                    </span>
                        <!-- Barra de progreso -->
                        <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner">
                            <div
                                    class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 transition-all duration-500 ease-out rounded-full"
                                    :style="`width: ${((current + 1) / videos.length) * 100}%`"
                            ></div>
                        </div>
                    </div>

                    <!-- Botón Siguiente -->
                    <button
                            @click="next"
                            class="group relative bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-full disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-110 hover:shadow-2xl active:scale-95 disabled:hover:scale-100 overflow-hidden"
                            :disabled="current === videos.length - 1"
                    >
                        <div class="flex items-center gap-2 relative z-10">
                            <span class="font-semibold hidden sm:inline">Suivant</span>
                            <flux:icon.arrow-right class="size-6 transition-transform duration-300 group-hover:translate-x-1" />
                        </div>
                        <!-- Efecto de brillo -->
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                        <!-- Efecto de onda -->
                        <span class="absolute inset-0 rounded-full bg-white opacity-0 group-active:opacity-30 transition-opacity duration-150"></span>
                    </button>
                </div>

                <!-- Botones de control mejorados -->
                <div class="flex justify-center gap-4 mt-6">
                    <!-- Play / Pause -->
                    <button
                            @click="togglePlay"
                            :disabled="isTransitioning"
                            class="group relative overflow-hidden px-6 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl"
                            :class="isPaused
                        ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white'
                        : 'bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white'"
                    >
                    <span
                            x-text="isPaused ? '▶️ Lecture' : '⏸️ Pause'"
                            class="relative z-10"
                    ></span>
                        <!-- Efecto shimmer -->
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </button>

                    <!-- Velocidad -->
                    <button
                            @click="toggleSpeed"
                            :disabled="isTransitioning"
                            class="group relative overflow-hidden px-6 py-3 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl"
                            :class="isSlow
                        ? 'bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 text-white'
                        : 'bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white'"
                    >
                    <span
                            x-text="isSlow ? '🐢 Lent' : '⚡ Normal'"
                            class="relative z-10"
                    ></span>
                        <!-- Efecto shimmer -->
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </button>
                </div>
            </div>
        @else
            <p class="text-center text-gray-500 dark:text-gray-400 text-lg">Aucune vidéo disponible</p>
        @endif
    </div>
</div>