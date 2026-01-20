@props([
    'video' => null,
    'type' => null, // recibe el tipo de pregunta
])

@if($video && $type !== 'video-choice')
    @php
        $videoId = pathinfo($video, PATHINFO_FILENAME);
        $optimizedUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/q_auto,w_1280,f_auto,c_limit/{$videoId}.mp4";
        $posterUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/so_0,w_400,q_auto:low/{$videoId}.jpg";

    @endphp

    <div class="mb-4" wire:ignore id="main-video-container">
        <video
                preload="metadata"
                src="{{ $optimizedUrl }}"
                poster="{{ $posterUrl }}"
                class="w-full h-auto max-h-[220px] object-cover rounded-lg"
                muted autoplay loop playsinline>
        </video>
    </div>
@endif