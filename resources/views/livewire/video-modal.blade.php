{{-- 🎬 MODAL VIDEO (vista separada) --}}
<flux:modal
        wire:model="open"
        x-data="{
            videoUrl: null,
            posterUrl: null,
            videoLoaded: false
        }"
        x-init="
        $watch('$wire.video', value => {
            if (value && value.url) {
                videoUrl = value.url;
                posterUrl = value.poster;
                videoLoaded = false; // Reset cuando cambia el video
            }
        })
    "
>
    <div class="space-y-4">

        {{-- Título --}}
        <h2 class="text-xl font-semibold text-center">
            {{ $video['title'] ?? '' }}
        </h2>

        {{-- Contenedor del video --}}
        <div class="relative w-full aspect-video">

            {{-- Skeleton mientras carga --}}
            <div x-show="!videoLoaded"
                 class="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg animate-pulse flex items-center justify-center">
                <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                </svg>
            </div>

            {{-- Video --}}
            <video
                    x-ref="myVideo"
                    x-bind:src="videoUrl"
                    x-bind:poster="posterUrl"
                    class="w-full rounded-lg transition-opacity duration-300"
                    :class="{ 'opacity-0': !videoLoaded }"
                    autoplay
                    muted
                    playsinline
                    preload="metadata"
                    @loadeddata="videoLoaded = true"
            ></video>
        </div>

        <button
                class="mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                @click="$refs.myVideo.currentTime = 0; $refs.myVideo.play();"
        >
            Revoir
        </button>

    </div>
</flux:modal>