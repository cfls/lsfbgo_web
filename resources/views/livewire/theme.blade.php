<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">

    {{-- Header sticky --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] md:pt-0 sticky top-0 z-10 shadow-md">
        <div class="max-w-5xl mx-auto px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center gap-3">
              <a
                wire:navigate
                href="{{ route('syllabus.themes', ['ue' => $this->ue, 'theme' => $this->theme]) }}"
                aria-label="Retour aux thèmes"
                class="text-white inline-flex items-center gap-2 hover:opacity-80 transition shrink-0"
                >
                <flux:icon.arrow-left-circle class="size-8 md:size-9" aria-hidden="true"/>
                </a>
                <span aria-hidden="true">
                    @include('partials.quiz.svg.logo', ['class' => 'w-12 h-12 md:w-16 md:h-16 shrink-0'])
                </span>
            </div>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="max-w-5xl mx-auto px-4 md:px-6 py-5 md:py-8">

        @if(count($videos) > 0)
            <div
                    x-data="{
                    videos: @js($videos),
                    current: {{ $currentIndex }},
                    isPaused: true,
                    isSlow: false,
                    repeatCount: 0,
                    isTransitioning: false,
                    videoLoaded: false,
                    get currentVideo() { return this.videos[this.current] || null; },
                    prev() {
                        if (this.current > 0) {
                            this.isTransitioning = true;
                            this.videoLoaded = false;
                            this.current--;
                            setTimeout(() => { this.resetVideo(); this.isTransitioning = false; }, 300);
                        }
                    },
                    next() {
                        if (this.current < this.videos.length - 1) {
                            this.isTransitioning = true;
                            this.videoLoaded = false;
                            this.current++;
                            setTimeout(() => { this.resetVideo(); this.isTransitioning = false; }, 300);
                        }
                    },
                    togglePlay() {
                        const video = this.$refs.player;
                        if (!video) return;
                        if (video.paused) { video.play(); this.isPaused = false; }
                        else { video.pause(); this.isPaused = true; }
                    },
                    toggleSpeed() {
                        const video = this.$refs.player;
                        if (!video) return;
                        this.isSlow = !this.isSlow;
                        video.playbackRate = this.isSlow ? 0.5 : 1;
                    },
                    resetVideo() {
                        const video = this.$refs.player;
                        if (!video) return;
                        video.pause();
                        video.currentTime = 0;
                        this.isPaused = true;
                        this.isSlow = false;
                        this.repeatCount = 0;
                        this.videoLoaded = false;
                        video.playbackRate = 1;
                        video.play().then(() => { this.isPaused = false; }).catch(() => {});
                        video.onended = () => {
                            if (this.repeatCount < 1) { this.repeatCount++; video.currentTime = 0; video.play(); }
                            else { this.isPaused = true; }
                        };
                    }
                }"
                    x-init="$nextTick(() => { if (currentVideo) resetVideo(); })"
                    class="space-y-5"
            >
                {{-- Título --}}
                <p
                        class="text-base md:text-xl text-zinc-800 dark:text-white text-center font-semibold transition-all duration-300 px-2"
                        x-text="currentVideo?.title"
                        :class="isTransitioning ? 'opacity-0 -translate-y-2' : 'opacity-100 translate-y-0'"
                ></p>

                {{-- Reproductor --}}
                <div class="relative w-full max-w-4xl mx-auto aspect-video rounded-xl overflow-hidden shadow-xl">

                    {{-- Skeleton --}}
                    <div
                            x-show="!videoLoaded"
                            class="absolute inset-0 bg-gray-900 flex items-center justify-center z-10"
                    >
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-10 h-10 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span class="text-white text-sm">Chargement...</span>
                        </div>
                    </div>

                    <video
                            x-ref="player"
                            class="w-full h-full object-cover transition-all duration-500"
                            :class="{
                            'opacity-0': !videoLoaded,
                            'opacity-100': videoLoaded,
                            'scale-95 blur-sm': isTransitioning,
                            'scale-100 blur-0': !isTransitioning
                        }"
                            :src="currentVideo?.url"
                            autoplay muted playsinline
                            @loadeddata="videoLoaded = true"
                    >
                        Votre navigateur ne supporte pas la vidéo.
                    </video>
                </div>

                {{-- Navegación anterior / progreso / siguiente --}}
                <div class="flex justify-center items-center gap-3 md:gap-6">

                    <button
                            @click="prev"
                            :disabled="current === 0"
                            class="group relative bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 md:px-6 py-2.5 md:py-3 rounded-full disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 hover:shadow-xl active:scale-95 disabled:hover:scale-100 overflow-hidden"
                    >
                        <div class="flex items-center gap-1.5 md:gap-2 relative z-10">
                            <flux:icon.arrow-left class="size-5 md:size-6 transition-transform group-hover:-translate-x-1" />
                            <span class="font-semibold text-sm md:text-base hidden sm:inline">Précédent</span>
                        </div>
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </button>

                    {{-- Progreso --}}
                    <div class="flex flex-col items-center px-2 md:px-4 min-w-[90px] md:min-w-[120px]">
                        <span class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <span x-text="current + 1" class="text-blue-600 dark:text-blue-400"></span>
                            <span class="text-gray-400">/</span>
                            <span x-text="videos.length"></span>
                        </span>
                        <div class="w-full h-1.5 md:h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner">
                            <div
                                    class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 transition-all duration-500 ease-out rounded-full"
                                    :style="`width: ${((current + 1) / videos.length) * 100}%`"
                            ></div>
                        </div>
                    </div>

                    <button
                            @click="next"
                            :disabled="current === videos.length - 1"
                            class="group relative bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 md:px-6 py-2.5 md:py-3 rounded-full disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 hover:shadow-xl active:scale-95 disabled:hover:scale-100 overflow-hidden"
                    >
                        <div class="flex items-center gap-1.5 md:gap-2 relative z-10">
                            <span class="font-semibold text-sm md:text-base hidden sm:inline">Suivant</span>
                            <flux:icon.arrow-right class="size-5 md:size-6 transition-transform group-hover:translate-x-1" />
                        </div>
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </button>
                </div>

                {{-- Controles play / velocidad --}}
                <div class="flex justify-center gap-3 md:gap-4">

                    <button
                            @click="togglePlay"
                            :disabled="isTransitioning"
                            class="group relative overflow-hidden px-5 md:px-6 py-2.5 md:py-3 rounded-full font-semibold text-sm md:text-base transition-all duration-300 hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="isPaused
                            ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white'
                            : 'bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white'"
                    >
                        <span x-text="isPaused ? '▶️ Lecture' : '⏸️ Pause'" class="relative z-10"></span>
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </button>

                    <button
                            @click="toggleSpeed"
                            :disabled="isTransitioning"
                            class="group relative overflow-hidden px-5 md:px-6 py-2.5 md:py-3 rounded-full font-semibold text-sm md:text-base transition-all duration-300 hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="isSlow
                            ? 'bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 text-white'
                            : 'bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white'"
                    >
                        <span x-text="isSlow ? '🐢 Lent' : '⚡ Normal'" class="relative z-10"></span>
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </button>
                </div>

            </div>

        @else
            <p class="text-center text-gray-500 dark:text-gray-400 text-lg py-20">
                Aucune vidéo disponible
            </p>
        @endif

    </div>
</div>