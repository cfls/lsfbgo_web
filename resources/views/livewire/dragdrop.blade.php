@push('styles')
    <style>
        img { touch-action: manipulation; }
        .game-container {
            max-height: 100vh;
            max-height: 100dvh;
            overflow: hidden;
        }
    </style>
@endpush

<div class="space-y-2 min-h-screen game-container">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                <flux:subheading size="xl" class="text-white text-base">
                    {{$title}}
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-2xl shadow max-w-5xl w-full mx-auto relative"
         x-data="{
            videoUrl: @entangle('currentWord.video'),
            key: @entangle('refreshKey'),
            selectedLetter: null,
            selectedSlot: null,
            completedGames: @entangle('completedGames').live,
            completed: @entangle('completed').live,
            isVisible: false,
            showSuccess: false,
            showFinal: false,
            hasStarted: false,
            successTimer: null,
            
            initVideo() {
                console.log('🎥 initVideo llamado, videoUrl:', this.videoUrl);
                console.log('🎥 videoPlayer existe?', !!$refs.videoPlayer);
                
                if ($refs.videoPlayer && this.videoUrl) {
                    this.isVisible = false;
                    $refs.videoPlayer.load();
                    setTimeout(() => {
                        $refs.videoPlayer.play().catch(err => {
                            console.error('❌ Error al reproducir:', err);
                        });
                        this.isVisible = true;
                    }, 400);
                } else {
                    console.warn('⚠️ No hay video URL o videoPlayer');
                }
            }
        }"
         x-init="
            console.log('🚀 Componente inicializado');
            console.log('📹 Video inicial:', videoUrl);
            console.log('📝 Palabra actual:', @js($currentWord));
            
            // ✅ Inicializar video al montar
            $nextTick(() => {
                setTimeout(() => initVideo(), 200);
            });
            
            // ✅ Observar cambios en videoUrl
            $watch('videoUrl', (value) => {
                console.log('🔄 VideoUrl cambió a:', value);
                if (value) {
                    setTimeout(() => initVideo(), 100);
                }
            });
            
            // Escuchar cuando se completa una palabra
            $watch('completed', (value) => {
                if (value === true && hasStarted) {
                    console.log('✅ Palabra completada detectada');
                    showSuccess = true;
                    
                    if (successTimer) clearTimeout(successTimer);
                    
                    successTimer = setTimeout(() => {
                        console.log('⏭️ Cargando siguiente palabra...');
                        showSuccess = false;
                        
                        if (completedGames >= 5) {
                            showFinal = true;
                            setTimeout(() => {
                                console.log('🏁 Redirigiendo...');
                                window.location.href = '/dragdrop';
                            }, 3000);
                        } else {
                            $wire.nextWord();
                        }
                    }, 2000);
                }
            });
        ">

        {{-- Progreso del juego --}}
        <div class="text-center mb-2">
            <p class="text-gray-700 text-sm font-medium">
                Jeu <span x-text="completedGames + 1"></span> / 5
            </p>
        </div>

        {{-- 🎉 Mensaje de éxito --}}
        <div x-show="showSuccess && hasStarted"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="bg-green-100 border-2 border-green-500 rounded-xl p-3 mb-3 text-center">
            <div class="text-4xl mb-2">🎉</div>
            <p class="text-green-700 text-lg font-bold">
                Mot complété!
            </p>
            <p class="text-green-600 text-sm">
                Chargement du prochain mot...
            </p>
        </div>

        {{-- 🎥 Video --}}
        <div class="flex justify-center mb-3">
            <template x-if="videoUrl">
                <video
                        x-ref="videoPlayer"
                        :key="key + '-' + videoUrl"
                        autoplay muted playsinline loop
                        class="rounded-xl shadow-md w-full max-w-[280px] h-[180px] object-cover transition-opacity duration-700"
                        :class="{'opacity-0': !isVisible, 'opacity-100': isVisible}">
                    <source :src="videoUrl" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            </template>

            <template x-if="!videoUrl">
                <p class="text-gray-500 italic text-center text-sm">
                    Vidéo non disponible
                </p>
            </template>
        </div>

        {{-- 🔤 Réponse --}}
            {{-- @if ($currentWord)
                <div class="text-center mb-2">
                    <p class="text-xs text-gray-400 uppercase tracking-widest mb-0.5">Réponse</p>
                    <p class="text-lg font-bold text-gray-700 tracking-wide">
                        {{ $currentWord['name'] ?? '' }}
                    </p>
                </div>
            @endif --}}

        {{-- 🧩 Slots --}}
        <div class="flex flex-wrap justify-center gap-1.5 mb-3">
            @foreach ($wordSlots as $i => $slot)
                <div
                        @click="
                        if (selectedLetter && !completed) {
                            hasStarted = true;
                            $wire.dropLetter({{ $i }}, selectedLetter);
                            selectedLetter = null;
                        }
                    "
                        @drop.prevent="
                        if (!completed) {
                            hasStarted = true;
                            $wire.dropLetter({{ $i }}, event.dataTransfer.getData('symbol'))
                        }
                    "
                        @dragover.prevent
                        @mouseenter="selectedSlot = {{ $i }}"
                        @mouseleave="selectedSlot = null"
                        @class([
                            'slot w-10 h-10 border-2 border-dashed rounded-lg flex items-center justify-center transition-all cursor-pointer',
                            'border-green-400 bg-green-50' => isset($slot['correct']) && $slot['correct'] === true,
                            'border-red-400 bg-red-50' => isset($slot['correct']) && $slot['correct'] === false,
                        ])
                        :class="{'ring-2 ring-blue-400 bg-blue-50/40': selectedSlot === {{ $i }} && !completed}">
                    @if ($slot && isset($slot['image']))
                        <img src="{{ $slot['image'] }}" class="w-8 h-8 rounded object-contain">
                    @endif
                </div>
            @endforeach
        </div>

        {{-- 🔠 Letras --}}
        @if ($currentWord)
            <div class="mb-2">
                <div class="grid gap-2 grid-cols-6 sm:grid-cols-7 md:grid-cols-8 place-items-center touch-manipulation select-none">
                    @foreach ($letters as $i => $letter)
                        <img
                                src="{{ $letter['image'] }}"
                                alt="{{ $letter['symbol'] }}"
                                @click="
                                if (!@js($letter['used'] ?? false) && !completed) {
                                    hasStarted = true;
                                    selectedLetter = '{{ $letter['symbol'] }}';
                                }
                            "
                                draggable="true"
                                ondragstart="event.dataTransfer.setData('symbol', '{{ $letter['symbol'] }}')"
                                class="w-11 h-11 object-contain rounded-lg shadow cursor-pointer
                                   hover:scale-110 active:scale-100 transition-transform duration-200
                                   touch-manipulation select-none"
                                :class="{
                                'ring-2 ring-blue-400 scale-110': selectedLetter === '{{ $letter['symbol'] }}',
                                'opacity-50 cursor-not-allowed': @js($letter['used'] ?? false) || completed
                            }">
                    @endforeach
                </div>
            </div>
        @endif

    
       {{-- 🎉 Modal final --}}
        <div x-show="showFinal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-x-0 bottom-16 z-50 flex flex-col items-center gap-3 pb-8 pt-6 rounded-t-3xl bg-gradient-to-br from-green-400 to-blue-500 text-white shadow-[0_-4px_16px_rgba(0,0,0,0.1)]"
            style="display: none;">
            <div class="text-5xl">🎉</div>
            <h1 class="text-2xl font-bold">Félicitations !</h1>
            <img src="{{ asset('img/lsfgo/good.png') }}" alt="Applaudissements" class="w-24" />
            <p class="font-semibold text-sm opacity-90">Vous avez complété tous les mots!</p>
            <p class="text-xs opacity-75">Redirection en cours...</p>
        </div>
    </div>
</div>
