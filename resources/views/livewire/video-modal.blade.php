<flux:modal
        wire:model="open"
        x-data="{
        videoUrl: null,
        posterUrl: null,
        videoLoaded: false,
        currentSpeed: 1
    }"
        x-init="
        $watch('$wire.open', isOpen => {
            if (isOpen && $wire.video && $wire.video.url) {
                videoLoaded = false;
                videoUrl = null;
                posterUrl = null;

                setTimeout(() => {
                    videoUrl = $wire.video.url.replace('w_1280', 'w_640');
                    posterUrl = $wire.video.poster;
                }, 100);
            }

            if (!isOpen) {
                if ($refs.myVideo) {
                    $refs.myVideo.pause();
                    $refs.myVideo.src = '';
                }

                videoUrl = null;
                videoLoaded = false;
                currentSpeed = 1;
            }
        })
    "
>
    <div class="space-y-4">

        {{-- Título --}}
        <h2 class="text-xl font-semibold text-center">
            {{ $video['title'] ?? '' }}
        </h2>

        {{-- Current Speed --}}
        <div class="text-center text-sm font-medium text-gray-700 dark:text-gray-200">
            Vitesse actuelle : <span x-text="currentSpeed + 'x'"></span>
        </div>

        {{-- Contenedor del video --}}
        <div class="relative w-full aspect-video">

            {{-- Skeleton mientras carga --}}
            <div
                    x-show="!videoLoaded"
                    class="absolute inset-0 bg-gray-900 rounded-lg flex items-center justify-center"
            >
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-12 h-12 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                        ></circle>
                        <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        ></path>
                    </svg>

                    <span class="text-white text-sm font-medium">
                        Chargement...
                    </span>
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
                    @loadeddata="
                    videoLoaded = true;
                    $refs.myVideo.playbackRate = currentSpeed;
                "
            ></video>
        </div>

        {{-- Botones velocidad --}}
        <div class="flex justify-center gap-2">
            <button
                    type="button"
                    class="px-3 py-1.5 text-sm rounded-lg border"
                    @click="currentSpeed = 0.5; $refs.myVideo.playbackRate = currentSpeed"
            >
                0.5x
            </button>

            <button
                    type="button"
                    class="px-3 py-1.5 text-sm rounded-lg border"
                    @click="currentSpeed = 0.75; $refs.myVideo.playbackRate = currentSpeed"
            >
                0.75x
            </button>

            <button
                    type="button"
                    class="px-3 py-1.5 text-sm rounded-lg border"
                    @click="currentSpeed = 1; $refs.myVideo.playbackRate = currentSpeed"
            >
                1x
            </button>
        </div>

    </div>
</flux:modal>