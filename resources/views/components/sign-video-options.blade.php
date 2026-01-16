@props([
    'options' => [],
    'correct' => null,
    'selected' => null,
    'answered' => false,
])

<div id="videochoice"
     class="w-full flex flex-wrap justify-center gap-4 mt-4"
     x-data="{
         selected: @entangle('selectedAnswer').live,
         answered: @entangle('answered').live,
         correct: @js($correct),

         selectOption(value) {
             if (!this.answered) {
                 this.selected = value;
                 $wire.call('selectAnswer', value);
             }
         },

         getOptionClass(value) {
             if (this.answered && value === this.correct) {
                 return 'border-green-500 ring-4 ring-green-300 scale-105 shadow-lg';
             }
             if (this.answered && this.selected === value && value !== this.correct) {
                 return 'border-red-600 ring-4 ring-red-300 scale-105 shadow-lg';
             }
             if (!this.answered && this.selected === value) {
                 return 'border-blue-500 ring-4 ring-blue-300 scale-105 shadow-lg';
             }
             return 'border-gray-300 scale-100 shadow-none';
         }
     }"
     x-init="$nextTick(() => window.initVideoChoiceHLS())">

    @foreach($options as $index => $option)
        @php
            $value = $option['value'] ?? '';
            $video = $option['video'] ?? '';
        @endphp

        <div
                class="relative w-[180px] sm:w-[200px] transition-transform duration-300 cursor-pointer"
                @click="selectOption(@js($value))">

            <video
                    data-hls-src="{{ $video }}"
                    data-option-value="{{ $value }}"
                    class="hls-choice-video w-full h-auto rounded-lg pointer-events-none"
                    muted loop playsinline
                    poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%23667eea' width='400' height='300'/%3E%3C/svg%3E">
            </video>

            <div
                    class="absolute inset-0 rounded-xl transition-all duration-300 pointer-events-none"
                    :class="getOptionClass(@js($value)) + (answered ? ' opacity-60' : '')">
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <script>
        let videoChoiceHlsInstances = new Map();

        window.initVideoChoiceHLS = function() {
            console.log('Inicializando video-choice HLS...');

            document.querySelectorAll('.hls-choice-video:not([data-hls-initialized])').forEach(video => {
                video.setAttribute('data-hls-initialized', 'true');

                const hlsSrc = video.getAttribute('data-hls-src');
                const optionValue = video.getAttribute('data-option-value');

                console.log('Procesando opción:', optionValue, hlsSrc);

                if (!hlsSrc) {
                    console.warn('No hay src para opción:', optionValue);
                    return;
                }

                // Safari - HLS nativo
                if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    console.log('Usando HLS nativo para opción:', optionValue);
                    video.src = hlsSrc;

                    video.addEventListener('loadedmetadata', () => {
                        console.log('Video cargado (nativo):', optionValue);
                        video.play().catch(err => {
                            console.error('Error al reproducir (nativo):', err);
                        });
                    }, { once: true });

                    video.load();
                }
                // Otros navegadores - hls.js
                else if (Hls.isSupported()) {
                    console.log('Usando hls.js para opción:', optionValue);

                    const hls = new Hls({
                        enableWorker: true,
                        lowLatencyMode: true,
                        backBufferLength: 90,
                        maxBufferLength: 30,
                        maxMaxBufferLength: 60,
                        startLevel: 0,
                        autoStartLoad: true
                    });

                    hls.loadSource(hlsSrc);
                    hls.attachMedia(video);

                    // Guardar instancia
                    if (videoChoiceHlsInstances.has(optionValue)) {
                        videoChoiceHlsInstances.get(optionValue).destroy();
                    }
                    videoChoiceHlsInstances.set(optionValue, hls);

                    hls.on(Hls.Events.MANIFEST_PARSED, () => {
                        console.log('Manifest parseado para opción:', optionValue);
                        video.play().catch(err => {
                            console.error('Error al reproducir:', err);
                        });
                    });

                    hls.on(Hls.Events.ERROR, (event, data) => {
                        console.error('Error HLS en opción:', optionValue, data);
                        if (data.fatal) {
                            switch(data.type) {
                                case Hls.ErrorTypes.NETWORK_ERROR:
                                    console.log('Recuperando de error de red...');
                                    hls.startLoad();
                                    break;
                                case Hls.ErrorTypes.MEDIA_ERROR:
                                    console.log('Recuperando de error de media...');
                                    hls.recoverMediaError();
                                    break;
                                default:
                                    console.log('Error fatal, destruyendo HLS...');
                                    hls.destroy();
                                    break;
                            }
                        }
                    });
                } else {
                    console.warn('HLS no soportado en este navegador');
                }
            });
        }

        // Inicializar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM loaded, inicializando video-choice...');
                setTimeout(() => window.initVideoChoiceHLS(), 100);
            });
        } else {
            console.log('DOM ya cargado, inicializando video-choice...');
            setTimeout(() => window.initVideoChoiceHLS(), 100);
        }

        // Limpiar al cambiar de pregunta
        Livewire.on('question-changed', () => {
            console.log('Pregunta cambiada, limpiando videos...');

            videoChoiceHlsInstances.forEach((hls, key) => {
                console.log('Destruyendo HLS opción:', key);
                hls.destroy();
            });
            videoChoiceHlsInstances.clear();

            // Resetear atributo de inicialización
            document.querySelectorAll('.hls-choice-video').forEach(v => {
                v.removeAttribute('data-hls-initialized');
            });

            // Reinicializar
            setTimeout(() => {
                console.log('Reinicializando videos después de cambio...');
                window.initVideoChoiceHLS();
            }, 200);
        });
    </script>
@endpush