@props(['video' => null, 'type' => null])

@if($video && $type !== 'video-choice')
    <div class="mb-4"
         wire:ignore
         id="main-video-container"
         x-data="{ src: '{{ $video }}' + '?v=' + Date.now() }">

        <video
                :src="src"
                muted
                autoplay
                loop
                playsinline
                class="w-full rounded-xl shadow">
        </video>

    </div>
@endif
