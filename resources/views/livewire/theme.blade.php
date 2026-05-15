<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] md:pt-0 shadow-md">
        <div class="max-w-5xl mx-auto px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center gap-3">
                <a wire:navigate href="{{ route('syllabus.themes', ['ue' => $this->ue, 'theme' => $this->theme]) }}"
                    aria-label="Retour aux thèmes"
                    class="text-white inline-flex items-center gap-2 hover:opacity-80 transition shrink-0">
                    <flux:icon.arrow-left-circle class="size-8 md:size-9" aria-hidden="true" />
                </a>
                <span aria-hidden="true">
                    @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20 shrink-0'])
                </span>
            </div>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="max-w-5xl mx-auto px-4 md:px-6 py-5 md:py-8">

        @if (count($videos) > 0)
            <div x-data="{
                videos: @js($videos),
                current: {{ $currentIndex }},
                activeSlot: 'a',
                isPaused: true,
                currentSpeed: 1,
                autoPlay: true,
                repeatCount: 0,
                videoLoaded: false,
                videoError: false,

                get currentVideo() { return this.videos[this.current] || null; },
                get isLast() { return this.current >= this.videos.length - 1; },

                activePlayer()   { return this.activeSlot === 'a' ? this.$refs.playerA : this.$refs.playerB; },
                inactivePlayer() { return this.activeSlot === 'a' ? this.$refs.playerB : this.$refs.playerA; },

                attachEnded(video) {
                    video.onended = () => {
                        if (!this.videoLoaded) return;
                        if (this.repeatCount < 1) {
                            this.repeatCount++;
                            video.currentTime = 0;
                            video.play();
                        } else {
                            if (!this.isLast && this.autoPlay) this.next();
                            else this.isPaused = true;
                        }
                    };
                },

                startActive(video) {
                    this.videoError  = false;
                    this.repeatCount = 0;
                    this.isPaused    = true;
                    video.onended    = null;
                    video.currentTime = 0;
                    video.playbackRate = this.currentSpeed;
                    video.play().then(() => { this.isPaused = false; }).catch(() => {});
                    this.attachEnded(video);
                },

                preloadInactive() {
                    const nextUrl  = this.videos[this.current + 1]?.url ?? '';
                    const inactive = this.inactivePlayer();
                    inactive.onended = null;
                    inactive.src = nextUrl;
                    if (nextUrl) inactive.load();
                },

                goTo(index) {
                    if (index < 0 || index >= this.videos.length) return;
                    const inactive    = this.inactivePlayer();
                    inactive.onended  = null;
                    const isForward   = index === this.current + 1;
                    const alreadyReady = isForward && inactive.readyState >= 3;
                    if (!alreadyReady) {
                        inactive.src = this.videos[index]?.url ?? '';
                        inactive.load();
                    }
                    this.current      = index;
                    this.videoLoaded  = alreadyReady;
                    this.activeSlot   = this.activeSlot === 'a' ? 'b' : 'a';
                    this.startActive(this.activePlayer());
                    this.preloadInactive();
                },

                next() { if (!this.isLast) this.goTo(this.current + 1); },

                togglePlay() {
                    const video = this.activePlayer();
                    if (video.paused) { video.play();  this.isPaused = false; }
                    else              { video.pause(); this.isPaused = true;  }
                },

                setSpeed(rate) {
                    this.currentSpeed = rate;
                    this.activePlayer().playbackRate = rate;
                },

                retryVideo() {
                    const video = this.activePlayer();
                    this.videoError  = false;
                    this.videoLoaded = false;
                    const src = video.src;
                    video.src = ''; video.load();
                    video.src = src; video.load();
                    video.play().then(() => { this.isPaused = false; }).catch(() => {});
                }
            }"
            x-init="$nextTick(() => {
                const v = $refs.playerA;
                v.src = videos[{{ $currentIndex }}]?.url ?? '';
                v.load();
                startActive(v);
                preloadInactive();
            })"
            class="space-y-5">

                {{-- Título --}}
                <p class="text-base md:text-xl text-zinc-800 dark:text-white text-center font-semibold px-2"
                    x-text="currentVideo?.title"></p>

                {{-- Reproductor --}}
                <div class="relative w-full max-w-4xl mx-auto aspect-video rounded-xl overflow-hidden shadow-xl bg-gray-900">

                    {{-- Skeleton --}}
                    <div x-show="!videoLoaded && !videoError"
                        class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-10 h-10 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span class="text-white text-sm">Chargement...</span>
                        </div>
                    </div>

                    {{-- Error --}}
                    <div x-show="videoError"
                        class="absolute inset-0 flex flex-col items-center justify-center z-10 gap-4">
                        <svg class="w-12 h-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <p class="text-white text-sm">La vidéo n'a pas pu se charger</p>
                        <button @click="retryVideo()"
                            class="px-5 py-2 bg-white text-gray-900 rounded-full font-semibold text-sm hover:bg-gray-200 transition">
                            🔄 Réessayer
                        </button>
                    </div>

                    {{-- Fin de todas las videos --}}
                    <div x-show="isLast && isPaused && videoLoaded && repeatCount >= 1"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center z-20 gap-3">
                        <div class="text-5xl">🎉</div>
                        <p class="text-white text-lg font-bold">Toutes les vidéos vues !</p>
                        <button @click="goTo(0)"
                            class="px-5 py-2 bg-white text-gray-900 rounded-full font-semibold text-sm hover:bg-gray-100 transition mt-1">
                            🔁 Recommencer depuis le début
                        </button>
                    </div>

                    {{-- Player A --}}
                    <video x-ref="playerA"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                        :class="activeSlot === 'a' ? 'opacity-100 z-[1]' : 'opacity-0 z-0'"
                        muted playsinline preload="auto"
                        x-on:loadeddata="if (activeSlot === 'a') videoLoaded = true"
                        x-on:error="if (activeSlot === 'a') videoError = true">
                    </video>

                    {{-- Player B --}}
                    <video x-ref="playerB"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                        :class="activeSlot === 'b' ? 'opacity-100 z-[1]' : 'opacity-0 z-0'"
                        muted playsinline preload="auto"
                        x-on:loadeddata="if (activeSlot === 'b') videoLoaded = true"
                        x-on:error="if (activeSlot === 'b') videoError = true">
                    </video>
                </div>

                {{-- Progreso --}}
                <div class="flex flex-col items-center">
                    <span class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <span x-text="current + 1" class="text-blue-600 dark:text-blue-400"></span>
                        <span class="text-gray-400">/</span>
                        <span x-text="videos.length"></span>
                    </span>
                    <div class="w-48 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 transition-all duration-500 ease-out rounded-full"
                            :style="`width: ${((current + 1) / videos.length) * 100}%`">
                        </div>
                    </div>
                </div>

                {{-- Controles --}}
                <div class="flex justify-center items-center gap-2 flex-wrap">

                    {{-- Auto / Manuel --}}
                    <button @click="autoPlay = !autoPlay"
                        class="px-5 py-2.5 rounded-full font-semibold text-sm transition-all duration-300 hover:scale-105 active:scale-95 shadow-lg"
                        :class="autoPlay
                            ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white'
                            : 'bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-200'">
                        <span x-text="autoPlay ? '🔁 ' : '✋'"></span>
                    </button>

                    {{-- Play / Pause --}}
                    <button @click="togglePlay"
                        class="group relative overflow-hidden px-5 py-2.5 rounded-full font-semibold text-sm transition-all duration-300 hover:scale-105 active:scale-95 shadow-lg"
                        :class="isPaused
                            ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white'
                            : 'bg-gradient-to-r from-red-500 to-rose-600 text-white'">
                        <span class="relative z-10 flex items-center gap-2">
                            {{-- ▶️ --}}
                            <svg x-show="isPaused" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            {{-- ⏸ --}}
                            <svg x-show="!isPaused" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                            </svg>
                            
                        </span>
                        <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </button>
                                        {{-- Velocidad — usar strings para evitar que PHP castee floats a int --}}
                    @foreach(['0.5' => '×0.5', '1.0' => '×1', '1.5' => '×1.5'] as $rate => $label)
                        <button @click="setSpeed({{ $rate }})"
                            :class="currentSpeed === {{ $rate }}
                                ? 'bg-teal-500 border-teal-500 text-white'
                                : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-200'"
                            class="h-10 px-4 rounded-full border text-sm font-bold transition active:scale-95 shadow-sm">
                            {{ $label }}
                        </button>
                    @endforeach

                </div>

            </div>
        @else
            <p class="text-center text-gray-500 dark:text-gray-400 text-lg py-20">
                Aucune vidéo disponible
            </p>
        @endif

    </div>
</div>