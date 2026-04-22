@push('styles')
    <style>
        img { touch-action: manipulation; }
        .game-container {
            max-height: 100vh;
            max-height: 100dvh;
            overflow: hidden;
        }

        {{-- ✅ prefers-reduced-motion: desactiva todas las animaciones/transiciones --}}
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition-duration: 0.01ms !important;
                animation-duration: 0.01ms !important;
            }
        }
    </style>
@endpush

<div class="space-y-2 min-h-screen game-container">

    {{-- Header --}}
  
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                {{-- ✅ Logo decorativo envuelto en span aria-hidden --}}
                <span aria-hidden="true">
                    @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                </span>

                {{-- ✅ as="h1" garantiza jerarquía de heading navegable con lector de pantalla --}}
                <flux:subheading as="h1" size="xl" class="text-white text-base">
                    {{ $title }}
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
                if ($refs.videoPlayer && this.videoUrl) {
                    this.isVisible = false;
                    $refs.videoPlayer.load();
                    setTimeout(() => {
                        $refs.videoPlayer.play().catch(() => {});
                        this.isVisible = true;
                    }, 400);
                }
            }
        }"
         x-init="
            $nextTick(() => {
                setTimeout(() => initVideo(), 200);
            });

            $watch('videoUrl', (value) => {
                if (value) setTimeout(() => initVideo(), 100);
            });

            $watch('completed', (value) => {
                if (value === true && hasStarted) {
                    showSuccess = true;
                    if (successTimer) clearTimeout(successTimer);
                    successTimer = setTimeout(() => {
                        showSuccess = false;
                        if (completedGames >= 5) {
                            showFinal = true;
                            setTimeout(() => {
                                window.location.href = '/dragdrop';
                            }, 3000);
                        } else {
                            $wire.nextWord();
                        }
                    }, 2000);
                }
            });
        ">

        {{-- ✅ Región aria-live assertive para anunciar éxito al lector de pantalla --}}
        <span
            class="sr-only"
            aria-live="assertive"
            aria-atomic="true"
            x-text="showSuccess ? 'Mot complété ! Chargement du mot suivant...' : ''"
        ></span>

        {{-- Progreso del juego --}}
        {{-- ✅ aria-live="polite" + aria-atomic para anunciar cambio de ronda --}}
        <div class="text-center mb-2">
            <p class="text-gray-700 text-sm font-medium"
               aria-live="polite"
               aria-atomic="true">
                Jeu <span x-text="completedGames + 1"></span> sur 5
            </p>
        </div>

        {{-- 🎉 Mensaje de éxito --}}
        <div x-show="showSuccess && hasStarted"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             role="status"
             class="bg-green-100 border-2 border-green-500 rounded-xl p-3 mb-3 text-center">
            {{-- ✅ Emoji decorativo --}}
            <div class="text-4xl mb-2" aria-hidden="true">🎉</div>
            <p class="text-green-700 text-lg font-bold">
                Mot complété !
            </p>
            <p class="text-green-600 text-sm">
                Chargement du prochain mot...
            </p>
        </div>

        {{-- 🎥 Video --}}
        <div class="flex justify-center mb-3">
            <template x-if="videoUrl">
                {{-- ✅ controls añadido: WCAG 2.2.2 permite pausar contenido animado --}}
                <video
                        x-ref="videoPlayer"
                        :key="key + '-' + videoUrl"
                        autoplay muted playsinline loop
                        controls
                        controlsList="nodownload nofullscreen"
                        oncontextmenu="return false"
                        aria-label="Vidéo du mot à deviner en langue des signes"
                        class="rounded-xl shadow-md  w-full md:w-1/2 h-auto object-cover transition-opacity duration-700"
                        :class="{'opacity-0': !isVisible, 'opacity-100': isVisible}"
                >
                    <source :src="videoUrl" type="video/mp4">
                    <p>Votre navigateur ne supporte pas la lecture vidéo.</p>
                </video>
            </template>

            <template x-if="!videoUrl">
                <p class="text-gray-500 italic text-center text-sm">
                    Vidéo non disponible
                </p>
            </template>
        </div>

        {{-- 🧩 Slots --}}
        {{-- ✅ Contexto semántico para el grupo de slots --}}
        <div
            role="group"
            aria-label="Emplacements pour former le mot"
            class="flex flex-wrap justify-center gap-1.5 mb-3"
        >
            @foreach ($wordSlots as $i => $slot)
                {{-- ✅ <button> en vez de <div>: focusable, semántico, aria-label con estado --}}
                <button
                    type="button"
                    aria-label="Emplacement {{ $i + 1 }}@if($slot && isset($slot['correct'])): @if($slot['correct'] === true) correct @else incorrect @endif @elseif($slot && isset($slot['symbol'])): {{ $slot['symbol'] }} placé @else vide @endif"
                    :aria-disabled="completed ? 'true' : 'false'"
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
                        'border-red-400 bg-red-50'   => isset($slot['correct']) && $slot['correct'] === false,
                    ])
                    :class="{'ring-2 ring-blue-400 bg-blue-50/40': selectedSlot === {{ $i }} && !completed}"
                >
                    @if ($slot && isset($slot['image']))
                        {{-- ✅ alt con el símbolo de la letra colocada --}}
                        <img
                            src="{{ $slot['image'] }}"
                            alt="{{ $slot['symbol'] ?? 'Lettre placée' }}"
                            class="w-8 h-8 rounded object-contain"
                        >
                    @endif
                </button>
            @endforeach
        </div>

        {{-- 🔠 Letras --}}
        @if ($currentWord)
            <div class="mb-2">
                {{-- ✅ role="group" con contexto semántico para el conjunto de letras --}}
                <div
                    role="group"
                    aria-label="Lettres disponibles à placer"
                    class="grid gap-2 grid-cols-6 sm:grid-cols-7 md:grid-cols-8 place-items-center touch-manipulation select-none"
                >
                    @foreach ($letters as $i => $letter)
                        {{-- ✅ <button> en vez de <img>: focusable y accesible por teclado --}}
                        <button
                            type="button"
                            :aria-label="'Lettre {{ $letter['symbol'] }}'"
                            :aria-pressed="selectedLetter === '{{ $letter['symbol'] }}' ? 'true' : 'false'"
                            :disabled="@js($letter['used'] ?? false) || completed"
                            :aria-disabled="(@js($letter['used'] ?? false) || completed) ? 'true' : 'false'"
                            @click="
                                if (!@js($letter['used'] ?? false) && !completed) {
                                    hasStarted = true;
                                    selectedLetter = '{{ $letter['symbol'] }}';
                                }
                            "
                            draggable="true"
                            ondragstart="event.dataTransfer.setData('symbol', '{{ $letter['symbol'] }}')"
                            class="rounded-lg shadow cursor-pointer
                                   hover:scale-110 active:scale-100 transition-transform duration-200
                                   touch-manipulation select-none p-0 bg-transparent border-0"
                            :class="{
                                'ring-2 ring-blue-400 scale-110': selectedLetter === '{{ $letter['symbol'] }}',
                                'opacity-50 cursor-not-allowed': @js($letter['used'] ?? false) || completed
                            }"
                        >
                            {{-- ✅ La imagen es decorativa: el button tiene el aria-label --}}
                            <img
                                src="{{ $letter['image'] }}"
                                alt=""
                                aria-hidden="true"
                                class="w-11 h-11 object-contain rounded-lg"
                            >
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 🎉 Modal final --}}
        {{-- ✅ role="dialog" + aria-modal + aria-labelledby + gestión de foco --}}
        <div
            x-show="showFinal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            x-effect="if (showFinal) $nextTick(() => $refs.finalTitle && $refs.finalTitle.focus())"
            role="dialog"
            aria-modal="true"
            aria-labelledby="final-title"
            class="fixed inset-x-0 bottom-16 z-50 flex flex-col items-center gap-3 pb-8 pt-6 rounded-t-3xl bg-gradient-to-br from-green-400 to-blue-500 text-white shadow-[0_-4px_16px_rgba(0,0,0,0.1)]"
            style="display: none;"
        >
            {{-- ✅ Emoji decorativo --}}
            <div class="text-5xl" aria-hidden="true">🎉</div>

            {{-- ✅ h2 (no h1, ya hay un h1 en el header) con tabindex para recibir foco programático --}}
            <h2
                id="final-title"
                x-ref="finalTitle"
                tabindex="-1"
                class="text-2xl font-bold outline-none"
            >
                Félicitations !
            </h2>

            <img src="{{ asset('img/lsfbgo/good.png') }}" alt="Applaudissements" class="w-24" />
            <p class="font-semibold text-sm opacity-90">Vous avez complété tous les mots !</p>

            {{-- ✅ aria-live para anunciar la redirección pendiente --}}
            <p class="text-xs opacity-75" aria-live="polite">
                Redirection dans 3 secondes…
            </p>
        </div>

    </div>
</div>