@push('styles')
    <style>
        @keyframes shake {
            0% { transform: translateX(0px); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0px); }
        }

        .animate-shake {
            animation: shake 0.35s ease-in-out;
        }

        /* SOLO apila en 1 columna si es <= 360px */
        @media (max-width: 360px) {
            .xs\:grid-cols-1 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }
    </style>
@endpush


<div class="w-full max-w-4xl mx-auto mt-6">

    <!-- GRID PRINCIPAL -->
    <div class="grid grid-cols-2 gap-6 xs:grid-cols-1">

        <!-- COLUMNA IZQUIERDA: PALABRAS -->
        <div class="space-y-4">

            @foreach ($pairsWords as $word)
                @php
                    $isCorrect = ($selectedMatches[$word] ?? null) === $word;
                    $isWrong = $wrongWord === $word;
                @endphp

                <button
                        wire:click="selectWord(@js($word))"
                        class="w-full px-4 py-3 rounded-xl border text-lg font-medium transition

                        @if($isCorrect)
                            bg-green-600 text-white border-green-600 border-4 opacity-70 cursor-not-allowed
                        @elseif($isWrong)
                            bg-red-600 text-white border-red-600 border-4 animate-shake
                        @elseif($selectedWord === $word)
                            bg-white text-black border-blue-500 border-4
                        @else
                            bg-white text-black border-gray-300
                        @endif
                    "
                        @if($isCorrect) disabled @endif
                >
                    {{ $word }}
                </button>
            @endforeach

        </div>

        <!-- COLUMNA DERECHA: VIDEOS -->
        <div class="space-y-4">

            @foreach ($pairsVideos as $pv)
                @php
                    $video = $pv['video'];
                    $videoId = pathinfo($video, PATHINFO_FILENAME);

                    // URL optimizada de Cloudinary (ajustado a 400px para mejor calidad)
                    $optimizedUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/q_auto:low,w_400,f_auto/{$videoId}.mp4";
                    $posterUrl = "https://res.cloudinary.com/dmhdsjmzf/video/upload/so_0,w_400,q_auto:low/{$videoId}.jpg";
                    $videoWord = $pv['word'];

                    $isCorrect = ($selectedMatches[$videoWord] ?? null) === $videoWord;
                    $isWrong = $wrongVideo === $videoWord;
                @endphp

                <div
                        wire:click="selectVideo(@js($videoWord))"
                        class="rounded-xl overflow-hidden cursor-pointer shadow transition

                        @if($isCorrect)
                            ring-4 ring-green-500 opacity-70 pointer-events-none
                        @elseif($isWrong)
                            ring-4 ring-red-600 animate-shake
                        @elseif($selectedVideo === $videoWord)
                            ring-4 ring-blue-500
                        @endif
                    "
                >
                    <video
                            preload="metadata"
                            src="{{ $optimizedUrl }}"
                            poster="{{ $posterUrl }}"
                            class="w-full h-auto max-h-[220px] object-cover"
                            muted autoplay loop playsinline>
                    </video>
                </div>

            @endforeach

        </div>

    </div>

</div>


@push('scripts')
    <script>
        Livewire.on('wrong-match', () => {
            setTimeout(() => {
                $wire.dispatch('clear-wrong');
            }, 800);
        });
    </script>
@endpush