@push('styles')
    <style>
        img { touch-action: manipulation; }
    </style>
@endpush
<div class="space-y-4  min-h-screen">

    <div
            class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none ">
        <div class="px-4">
            <div class="p-2 inline-block">
                @include('partials.quiz.svg.logo')
                <flux:subheading class="text-white text-xl pb-4">
                     {{$title}}
                </flux:subheading>
            </div>
        </div>
    </div>
    <div
            class="bg-white p-4 sm:p-6 md:p-8 rounded-2xl shadow max-w-5xl w-full mx-auto relative"
            x-data="{
        videoUrl: @entangle('currentWord.video'),
        key: @entangle('refreshKey'),
        selectedLetter: null,
        selectedSlot: null,
        showModal: false,
        showDemoModal: false,
        isPlaying: @entangle('isPlaying'),
        completedGames: @entangle('completedGames'),
        isVisible: false,
        hasSubscription: @entangle('hasSubscription'),
        demoPlayed: @entangle('demoPlayed'),
        initVideo() {
            if ($refs.videoPlayer && this.videoUrl) {
                this.isVisible = false;
                $refs.videoPlayer.load();
                setTimeout(() => {
                    $refs.videoPlayer.play();
                    this.isVisible = true;
                }, 400);
            }
        }
    }"
            x-init="
        // 🎬 Cargar video inicial
        $nextTick(() => initVideo());

        // 👀 Recargar video cuando cambia
        $watch('videoUrl', value => {
            if ($refs.videoPlayer) {
                isVisible = false;
                $refs.videoPlayer.load();
                setTimeout(() => {
                    if ($refs.videoPlayer) {
                        $refs.videoPlayer.play();
                        isVisible = true;
                    }
                }, 400);
            }
        });

        // 🎉 Mostrar modal de éxito
        $watch('$wire.completed', value => {
            if (value) {
                setTimeout(() => showModal = true, 300);
            }
        });

        // 🔒 Mostrar modal de demo terminada
        Livewire.on('demo-ended', () => {
            showDemoModal = true;
        });
    "
    >



        <!-- 🔢 Progression du jeu -->
        <div class="text-center mb-4">
            <p class="text-gray-700 text-base sm:text-lg font-medium">
                Jeu <span x-text="completedGames"></span> sur 5
            </p>
        </div>
        {{-- 🎥 Video centrado --}}
        <div class="flex justify-center mb-6">
            <template x-if="videoUrl">
                <video
                        x-ref="videoPlayer"
                        :key="key + '-' + videoUrl"
                        autoplay muted playsinline
                        loop
                        class="rounded-2xl shadow-md w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl transition-opacity duration-700 aspect-video"
                        :class="{'opacity-0': !isVisible, 'opacity-100': isVisible}"
                >
                    <source :src="videoUrl" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            </template>

            <template x-if="!videoUrl">
                <p class="text-gray-500 italic text-center text-sm sm:text-base">
                    Vidéo non disponible
                </p>
            </template>
        </div>

        {{-- 🧩 Espacios (slots) --}}
        <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-6 sm:mb-8">
            @foreach ($slots as $i => $slot)
                <div
                        @click="
                    if (selectedLetter) {
                        $wire.dropLetter({{ $i }}, selectedLetter);
                        selectedLetter = null;
                    }
                "
                        @drop.prevent="$wire.dropLetter({{ $i }}, event.dataTransfer.getData('symbol'))"
                        @dragover.prevent
                        @mouseenter="selectedSlot = {{ $i }}"
                        @mouseleave="selectedSlot = null"
                        @class([
                            'slot w-12 h-12 sm:w-16 sm:h-16 border-2 border-dashed rounded-xl flex items-center justify-center transition-all cursor-pointer',
                            'border-green-400 bg-green-50' => isset($slot['correct']) && $slot['correct'] === true,
                            'border-red-400 bg-red-50' => isset($slot['correct']) && $slot['correct'] === false,
                        ])
                        :class="{'ring-2 ring-blue-400 bg-blue-50/40': selectedSlot === {{ $i }}}"
                >
                    @if ($slot && isset($slot['image']))
                        <img src="{{ $slot['image'] }}" class="w-10 h-10 sm:w-14 sm:h-14 rounded-lg object-contain">
                    @endif
                </div>
            @endforeach
        </div>


        {{-- 🔠 Letras (en grid adaptable sin scroll) --}}
        @if ($currentWord)
            <div class="mb-4 sm:mb-6">
                <div
                        class="grid gap-3 sm:gap-4
            grid-cols-5 xs:grid-cols-6 sm:grid-cols-7 md:grid-cols-8 lg:grid-cols-10
            place-items-center touch-manipulation select-none"
                >
                    @foreach ($letters as $i => $letter)
                        <img
                                src="{{ $letter['image'] }}"
                                alt="{{ $letter['symbol'] }}"
                                @click="
                        if (!@js($letter['used'] ?? false)) {
                            selectedLetter = '{{ $letter['symbol'] }}';
                        }
                    "
                                draggable="true"
                                ondragstart="event.dataTransfer.setData('symbol', '{{ $letter['symbol'] }}')"

                                class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20
                    object-contain rounded-xl shadow cursor-pointer
                    hover:scale-110 active:scale-100 transition-transform duration-200
                    touch-manipulation select-none"
                                :class="{
                        'ring-4 ring-blue-400 scale-110': selectedLetter === '{{ $letter['symbol'] }}'
                    }"
                        >
                    @endforeach
                </div>
            </div>
        @endif


        {{-- 🎉 Modal éxito --}}
        <div
                x-show="showModal || isPlaying"
                x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                x-cloak
        >
            <div
                    @click.away="showModal = false"
                    class="bg-white w-full max-w-xs sm:max-w-sm md:max-w-md rounded-2xl p-4 sm:p-6 shadow-lg text-center relative"
                    x-transition.scale
            >
                <button
                        @click="showModal = false"
                        class="absolute top-2 right-3 text-gray-400 hover:text-gray-600 text-2xl font-bold"
                >&times;</button>

                <!-- 🔹 Progreso de partidas -->
                <p class="text-gray-600 text-sm mb-3">
                    Partie <span x-text="completedGames"></span> / 5
                </p>

                <div class="text-green-600 text-5xl mb-3">🎉</div>
                <img src="img/aplause.png" alt="Applaudissements" class="w-24 h-24 sm:w-32 sm:h-32 mx-auto my-4" >
                <p class="text-gray-700 mb-4 text-sm sm:text-base">
                    Vous avez complété le mot <strong class="uppercase">{{ strtoupper($currentWord['name'] ?? '') }}</strong>
                </p>

                <button
                        wire:click="nextWord"
                        @click="showModal = false"
                        class="mt-3 px-4 py-2 sm:px-6 sm:py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition text-sm sm:text-base"
                >
                    Mot suivant
                </button>
            </div>
        </div>

        {{-- 🔒 Modal demo terminada --}}
        {{--    <div--}}
        {{--            x-show="showDemoModal"--}}
        {{--            x-transition.opacity--}}
        {{--            class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50"--}}
        {{--            x-cloak >--}}
        {{--        <div class="bg-white rounded-2xl p-6 w-96 text-center shadow-lg relative animate-scale-in">--}}
        {{--            <p class="text-2xl font-bold mb-3">🔒 Contenu réservé</p>--}}
        {{--            <p class="text-gray-600 mb-4">--}}
        {{--                Vous avez terminé la version d'essai.<br>--}}
        {{--                Pour continuer à jouer, abonnez-vous maintenant !--}}
        {{--            </p>--}}

        {{--            <a href="{{ route('plan.index') }}"--}}
        {{--               class="px-6 py-2 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition">--}}
        {{--                💳 S’abonner maintenant--}}
        {{--            </a>--}}
        {{--        </div>--}}
        {{--    </div>--}}
        <!-- 🎉 Modal final (5 juegos completados) -->
        <div
                x-data="{ openFinalModal: false, score: 0, nextTheme: null }"
                @game-completed.window="
        openFinalModal = true;
        setTimeout(() => {
        window.location.href = `/syllabus/{{ $slug }}/{{ $type }}`;
        }, 4000);
    ">
            <div
                    x-show="openFinalModal"
                    x-transition.opacity.duration.400ms
                    class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
            >
                <div class="bg-white rounded-2xl shadow-xl p-6 text-center max-w-sm mx-auto animate-fadeIn">
                    <h1 class="text-2xl font-bold text-green-600 mb-3">🎉 Félicitations !</h1>
                    <img src="{{ asset('img/aplause.png') }}" alt="Applaudissements" class="mx-auto w-32 h-32 mb-4" />



                    <p class="text-blue-600 font-semibold mt-2">Passage au thème suivant...</p>
                </div>
            </div>
        </div>
    </div>
</div>
