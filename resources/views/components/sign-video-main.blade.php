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
        $optimizedUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/q_auto,w_1280,f_auto,c_limit/{$videoId}.mp4";
        $posterUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/so_0,w_400,q_auto:low/{$videoId}.jpg";
    @endphp

    <div class="mb-4" wire:ignore id="main-video-container">
        <video
                preload="metadata"
                src="{{ encode_cloudinary_url($optimizedUrl) }}"
                controlsList="nodownload"
                poster="{{ $posterUrl }}"
                class="w-full h-auto max-h-[220px] object-cover rounded-lg"
                muted autoplay loop playsinline>
        </video>
    </div>
@endif