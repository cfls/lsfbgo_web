{{-- 🎬 MODAL VIDEO (vista separada) --}}
<flux:modal
        wire:model="open"
        x-data="{
        videoUrl: null,
        posterUrl: null,
        videoLoaded: false
    }"
        x-init="
        $watch('$wire.open', isOpen => {
            if (isOpen && $wire.video && $wire.video.url) {
                videoLoaded = false;
                videoUrl = null;
                posterUrl = null;

                // Pequeño delay para que el modal esté visible antes de cargar
                setTimeout(() => {
                    // Usar resolución más baja para mobile
                    videoUrl = $wire.video.url.replace('w_1280', 'w_640');
                    posterUrl = $wire.video.poster;
                }, 100);
            }

            if (!isOpen) {
                // Pausar y limpiar cuando se cierra
                if ($refs.myVideo) {
                    $refs.myVideo.pause();
                    $refs.myVideo.src = '';
                }
                videoUrl = null;
                videoLoaded = false;
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
                 class="absolute inset-0 bg-gray-900 rounded-lg flex items-center justify-center">

                {{-- Spinner --}}
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-12 h-12 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-white text-sm font-medium">Chargement...</span>
                </div>
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
                loop
                playsinline
                preload="metadata"
                @loadeddata="videoLoaded = true"
            ></video>
        </div>

        {{-- <button
                class="mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                @click="$refs.myVideo.currentTime = 0; $refs.myVideo.play();"
        >
            Revoir
        </button> --}}

    </div>
</flux:modal>