@props([
    'video' => null,
    'type' => null,
])

@php
    function encode_cloudinary_url($url) {
        if (empty($url)) return $url;

        $search = ['à', 'è', 'é', 'ê', 'î', 'ô', 'û', 'ù', 'ë', 'ï', 'ü', 'ç', 'À', 'È', 'É', 'Ê', 'Î', 'Ô', 'Û', 'Ù', 'Ë', 'Ï', 'Ü', 'Ç'];
        $replace = array_map('rawurlencode', $search);
        $url = str_replace($search, $replace, $url);

        if (strpos($url, '?_a=') === false && strpos($url, '&_a=') === false) {
            $separator = strpos($url, '?') !== false ? '&' : '?';
            $url .= $separator . '_a=BAAAV6GY';
        }

        return $url;
    }
@endphp

@if($video && $type !== 'video-choice')
    @php
        $videoId = pathinfo($video, PATHINFO_FILENAME);
        $optimizedUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/q_auto,w_640,f_auto,c_limit/{$videoId}.mp4";
        $posterUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/so_0,w_400,q_auto:low/{$videoId}.jpg";
    @endphp

    <div class="mb-4 relative" wire:ignore id="main-video-container"
         x-data="{ videoLoaded: false }">

        {{-- Spinner mientras carga --}}
        <div x-show="!videoLoaded"
             class="absolute inset-0 bg-gray-900 rounded-lg flex items-center justify-center z-10">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-10 h-10 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-white text-sm">Chargement...</span>
            </div>
        </div>

        <video
                preload="metadata"
                src="{{ encode_cloudinary_url($optimizedUrl) }}"
                controlsList="nodownload"
                poster="{{ $posterUrl }}"
                class="w-full h-auto max-h-[220px] object-cover rounded-lg transition-opacity duration-300"
                :class="{ 'opacity-0': !videoLoaded }"
                muted autoplay loop playsinline
                @loadeddata="videoLoaded = true"
        ></video>
    </div>
@endif