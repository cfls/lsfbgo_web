@props([
    'video' => null,
    'type' => null, // recibe el tipo de pregunta
])

@if($video && $type !== 'video-choice') {{-- ✅ Ocultar cuando es video-choice --}}
<div class="mb-4"
     wire:ignore
     id="main-video-container">


    <video
            src="{{$video}}"
            muted
            autoplay
            loop
            playsinline>
    </video>

</div>
@endif
