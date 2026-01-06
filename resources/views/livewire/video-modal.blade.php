{{-- 🎬 MODAL VIDEO (vista separada) --}}
<flux:modal
        wire:model="open"
        x-data="{ videoUrl: null }"
        x-init="
        $watch('$wire.video', value => {
            if (value && value.url) {
                videoUrl = value.url;
            }
        })
    "
>
    <div class="space-y-4">

        {{-- Título --}}
        <h2 class="text-xl font-semibold text-center">
            {{ $video['title'] ?? '' }}
        </h2>

        {{-- Video --}}
        <video
                x-ref="myVideo"
                x-bind:src="videoUrl"
                class="w-full rounded-lg"
                autoplay
                muted
                playsinline
        ></video>

        <button
                class="mt-3 px-4 py-2 bg-blue-600 text-white rounded"
                @click="$refs.myVideo.currentTime = 0; $refs.myVideo.play();"
        >
            Rejouer
        </button>

    </div>
</flux:modal>
