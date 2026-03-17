@push('styles')
<style>
    .game-container {
        max-height: 100vh;
        max-height: 100dvh;
        overflow: hidden;
    }
    .speed-btn {
        position: relative;
        overflow: hidden;
    }
    .speed-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, white 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .speed-btn:active::after { opacity: 0.2; }
</style>
@endpush

<div class="min-h-screen game-container flex flex-col bg-gray-50 dark:bg-zinc-950">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] shrink-0">
        <div class="px-4 py-3 flex items-center gap-3">
            @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
            <flux:subheading size="xl" class="text-white text-base font-semibold">
                {{ $title }}
            </flux:subheading>
        </div>
    </div>

    {{-- Player --}}
    <div class="flex-1 flex flex-col px-4 py-5 gap-5"
        x-data="{
    speeds: [0.6, 1, 2],
    speedIndex: 1,
    paused: false,
    loading: true,
    togglePause() {
        if (this.paused) { this.$refs.video.play(); }
        else              { this.$refs.video.pause(); }
        this.paused = !this.paused;
    }
}">

        {{-- Video --}}
        <div class="relative rounded-2xl overflow-hidden bg-black shadow-xl">

            {{-- Loader --}}
            <div x-show="loading"
                 class="absolute inset-0 bg-zinc-900 flex items-center justify-center z-10">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-9 h-9 text-teal-400 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="text-white/60 text-xs tracking-widest uppercase">Chargement…</span>
                </div>
            </div>

            <video x-ref="video"
                   class="w-full aspect-video object-cover"
                   autoplay muted loop playsinline
                   @loadeddata="loading = false"
                   @waiting="loading = true"
                   @playing="loading = false">
                <source src="https://res.cloudinary.com/dmhdsjmzf/video/upload/v1748439117/LES_CHIFFRES_ohbdhd.mp4"
                        type="video/mp4">
            </video>
        </div>

        {{-- Controles --}}
        {{-- Controles --}}
<div class="flex items-center justify-center gap-3 py-2 mt-3">

    {{-- Velocidades --}}
    @foreach (['0.6' => '×0.5', '1' => '×1', '2' => '×2'] as $speed => $label)
        <button
            class="h-11 px-5 rounded-full border text-sm font-bold transition active:scale-95 shadow-sm"
            :class="speeds[speedIndex] === {{ $speed }}
                ? 'bg-teal-500 border-teal-500 text-white'
                : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-200'"
            @click="speedIndex = speeds.indexOf({{ $speed }}); $refs.video.playbackRate = {{ $speed }}"
        >
            {{ $label }}
        </button>
    @endforeach

   

 

</div>
     <div class="flex justify-center">
          {{-- Play / Pause --}}
    <button
        class="w-12 h-12 rounded-full flex items-center justify-center shadow-sm border transition active:scale-95"
        :class="paused
            ? 'bg-teal-500 border-teal-500 text-white'
            : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-200'"
        @click="togglePause()"
    >
        <svg x-show="paused" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 ml-0.5">
            <path d="M8 5v14l11-7z"/>
        </svg>
        <svg x-show="!paused" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
        </svg>
    </button>
     </div>
    </div>
</div>