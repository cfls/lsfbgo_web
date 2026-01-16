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

        video {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        @media (max-width: 360px) {
            .xs\:grid-cols-1 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }
    </style>
@endpush

<div class="w-full max-w-4xl mx-auto mt-6"
     x-data="{
         selectedWord: @entangle('selectedWord').live,
         selectedVideo: @entangle('selectedVideo').live,
         selectedMatches: @js($selectedMatches),
         wrongWord: @entangle('wrongWord').live,
         wrongVideo: @entangle('wrongVideo').live,

         selectWord(word) {
             this.selectedWord = word;
             $wire.call('selectWord', word);
         },

         selectVideo(videoWord) {
             this.selectedVideo = videoWord;
             $wire.call('selectVideo', videoWord);
         },

         isCorrect(word) {
             return this.selectedMatches[word] === word;
         },

         getWordClass(word) {
             if (this.isCorrect(word)) {
                 return 'bg-green-600 text-white border-green-600 border-4 opacity-70 cursor-not-allowed';
             }
             if (this.wrongWord === word) {
                 return 'bg-red-600 text-white border-red-600 border-4 animate-shake';
             }
             if (this.selectedWord === word) {
                 return 'bg-white text-black border-blue-500 border-4';
             }
             return 'bg-white text-black border-gray-300';
         },

         getVideoClass(videoWord) {
             if (this.isCorrect(videoWord)) {
                 return 'ring-4 ring-green-500 opacity-70 pointer-events-none';
             }
             if (this.wrongVideo === videoWord) {
                 return 'ring-4 ring-red-600 animate-shake';
             }
             if (this.selectedVideo === videoWord) {
                 return 'ring-4 ring-blue-500';
             }
             return '';
         }
     }"
     @update-matches.window="selectedMatches = $event.detail"
     x-init="$nextTick(() => window.initHLSVideos())">

    <div class="grid grid-cols-2 gap-6 xs:grid-cols-1">

        <!-- COLUMNA IZQUIERDA: PALABRAS -->
        <div class="space-y-4">
            @foreach ($pairsWords as $index => $word)
                <button
                        @click="selectWord(@js($word))"
                        :class="getWordClass(@js($word))"
                        class="w-full px-4 py-3 rounded-xl border text-lg font-medium transition"
                        :disabled="isCorrect(@js($word))">
                    {{ $word }}
                </button>
            @endforeach
        </div>

        <!-- COLUMNA DERECHA: VIDEOS HLS -->
        <div class="space-y-4">
            @foreach ($pairsVideos as $index => $pv)
                @php
                    $video = $pv['video'];
                    $videoWord = $pv['word'];
                @endphp

                <div
                        @click="selectVideo(@js($videoWord))"
                        :class="getVideoClass(@js($videoWord))"
                        class="rounded-xl overflow-hidden cursor-pointer shadow transition">

                    <video
                            data-hls-src="{{ $video }}"
                            data-video-word="{{ $videoWord }}"
                            class="w-full h-auto max-h-[220px] object-cover hls-video"
                            muted loop playsinline
                            poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%23667eea' width='400' height='300'/%3E%3C/svg%3E">
                    </video>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
    <!-- Cargar hls.js desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <script>
        let hlsInstances = new Map();

        // Hacer la función global para que Alpine pueda llamarla
        window.initHLSVideos = function() {
            console.log('Inicializando videos HLS...');

            document.querySelectorAll('.hls-video:not([data-hls-initialized])').forEach(video => {
                video.setAttribute('data-hls-initialized', 'true');

                const hlsSrc = video.getAttribute('data-hls-src');
                const videoWord = video.getAttribute('data-video-word');

                console.log('Procesando video:', videoWord, hlsSrc);

                if (!hlsSrc) {
                    console.warn('No hay src para:', videoWord);
                    return;
                }

                // Verificar si el navegador soporta HLS nativamente (Safari)
                if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    console.log('Usando HLS nativo para:', videoWord);
                    video.src = hlsSrc;

                    video.addEventListener('loadedmetadata', () => {
                        console.log('Video cargado (nativo):', videoWord);
                        video.play().catch(err => {
                            console.error('Error al reproducir (nativo):', err);
                        });
                    }, { once: true });

                    video.load();
                }
                // Usar hls.js para otros navegadores
                else if (Hls.isSupported()) {
                    console.log('Usando hls.js para:', videoWord);

                    const hls = new Hls({
                        enableWorker: true,
                        lowLatencyMode: true,
                        backBufferLength: 90,
                        maxBufferLength: 30,
                        maxMaxBufferLength: 60,
                        startLevel: 0,
                        autoStartLoad: true // CAMBIADO a true para cargar inmediatamente
                    });

                    hls.loadSource(hlsSrc);
                    hls.attachMedia(video);

                    // Guardar instancia
                    if (hlsInstances.has(videoWord)) {
                        hlsInstances.get(videoWord).destroy();
                    }
                    hlsInstances.set(videoWord, hls);

                    hls.on(Hls.Events.MANIFEST_PARSED, () => {
                        console.log('Manifest parseado para:', videoWord);
                        video.play().catch(err => {
                            console.error('Error al reproducir:', err);
                        });
                    });

                    hls.on(Hls.Events.ERROR, (event, data) => {
                        console.error('Error HLS:', videoWord, data);
                        if (data.fatal) {
                            switch(data.type) {
                                case Hls.ErrorTypes.NETWORK_ERROR:
                                    console.log('Intentando recuperar del error de red...');
                                    hls.startLoad();
                                    break;
                                case Hls.ErrorTypes.MEDIA_ERROR:
                                    console.log('Intentando recuperar del error de media...');
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
                console.log('DOM loaded, inicializando videos...');
                setTimeout(() => window.initHLSVideos(), 100);
            });
        } else {
            console.log('DOM ya cargado, inicializando videos...');
            setTimeout(() => window.initHLSVideos(), 100);
        }

        // Limpiar instancias cuando cambies de práctica
        Livewire.on('practice-changed', () => {
            console.log('Práctica cambiada, limpiando videos...');

            // Destruir todas las instancias HLS anteriores
            hlsInstances.forEach((hls, key) => {
                console.log('Destruyendo HLS:', key);
                hls.destroy();
            });
            hlsInstances.clear();

            // Resetear atributo de inicialización
            document.querySelectorAll('.hls-video').forEach(v => {
                v.removeAttribute('data-hls-initialized');
            });

            // Esperar a que el DOM se actualice
            setTimeout(() => {
                console.log('Reinicializando videos después de cambio...');
                window.initHLSVideos();
            }, 200);
        });

        // Actualizar matches sin re-renderizar
        Livewire.on('match-updated', (data) => {
            console.log('Match actualizado:', data);
            window.dispatchEvent(new CustomEvent('update-matches', {
                detail: data.matches
            }));
        });

        Livewire.on('wrong-match', () => {
            setTimeout(() => {
                $wire.dispatch('clear-wrong');
            }, 800);
        });
    </script>
@endpush