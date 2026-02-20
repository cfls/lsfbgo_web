@props([
    'options' => [],
    'correct' => null,
    'selected' => null,
    'answered' => false,
])

<div id="videochoice" class="w-full flex flex-wrap justify-center gap-4 mt-4">
    @if(is_array($options) && count($options) > 0)
        @foreach($options as $index => $option)
            @php
                $value = $option['value'] ?? '';
                $video = $option['video'] ?? '';
                $videoId = pathinfo($video, PATHINFO_FILENAME);

                // URL optimizada de Cloudinary
                $optimizedUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/q_auto,w_1280,f_auto,c_limit/{$videoId}.mp4";
                $posterUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/so_0,w_200,q_auto:low/{$videoId}.jpg";
            @endphp

            <div class="relative w-[180px] sm:w-[200px] transition-transform duration-300"
                 wire:ignore.self
                 x-data="{
                    value: @js($value),
                    correct: @js($correct),
                    selected: @entangle('selectedAnswer'),
                    answered: @entangle('answered'),
                    videoLoaded: false,
                    videoElement: null,
                    init() {
                        this.$nextTick(() => {
                            this.videoElement = this.$refs.video;
                            if (this.videoElement) {
                                this.videoElement.load();
                            }
                        });
                    }
                 }"
                 @click.stop="if (!answered) { selected = value; $wire.selectAnswer(value) }"
            >
                <!-- Skeleton mientras carga -->
                <div x-show="!videoLoaded"
                     class="absolute inset-0 bg-gray-900 rounded-lg flex items-center justify-center z-10 w-full aspect-video">
                    <svg class="w-8 h-8 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>

                <video
                        x-ref="video"
                        id="option-{{ $index }}"
                        class="w-full h-auto rounded-lg pointer-events-none transition-opacity duration-300"
                        :class="{ 'opacity-0 absolute': !videoLoaded }"
                        src="{{ $optimizedUrl }}"
                        poster="{{ $posterUrl }}"
                        muted loop playsinline
                        preload="metadata"
                        @loadeddata="
                        videoLoaded = true;
                        $el.play().catch(err => console.log('Play prevented:', err))"

                ></video>

                <div class="absolute inset-0 rounded-xl transition-all duration-300 pointer-events-none"
                     :class="{
                        'border-green-500 ring-4 ring-green-300 scale-105 shadow-lg': answered && value === correct,
                        'border-red-600 ring-4 ring-red-300 scale-105 shadow-lg': answered && selected === value && value !== correct,
                        'border-blue-500 ring-4 ring-blue-300 scale-105 shadow-lg': !answered && selected === value,
                        'border-gray-300 scale-100 shadow-none': !answered && selected !== value,
                        'opacity-60': answered
                     }">
                </div>
            </div>
        @endforeach
    @else
        <div class="w-full text-center text-gray-500 py-8">
            No hay opciones de video disponibles
        </div>
    @endif
</div>