@props(['video' => null, 'type' => null])

@if($video && $type !== 'video-choice')
    <div class="mb-4"
         wire:ignore
         id="main-video-container"
         x-data="{
             videoSrc: @js($video),
             initVideo() {
                 $nextTick(() => {
                     if (window.initMainVideoHLS) {
                         window.initMainVideoHLS();
                     }
                 });
             }
         }"
         x-init="initVideo()">

        <video
                data-hls-src="{{ $video }}"
                class="main-hls-video w-full rounded-xl shadow"
                muted
                loop
                playsinline
               >
        </video>

    </div>
@endif

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <script>
        let mainVideoHlsInstance = null;

        window.initMainVideoHLS = function() {
            console.log('Inicializando video principal HLS...');

            const video = document.querySelector('.main-hls-video:not([data-hls-initialized])');

            if (!video) {
                console.warn('No se encontró video principal');
                return;
            }

            video.setAttribute('data-hls-initialized', 'true');

            const hlsSrc = video.getAttribute('data-hls-src');

            console.log('Video principal src:', hlsSrc);

            if (!hlsSrc) {
                console.warn('No hay src para video principal');
                return;
            }

            // Safari - HLS nativo
            if (video.canPlayType('application/vnd.apple.mpegurl')) {
                console.log('Usando HLS nativo para video principal');
                video.src = hlsSrc;

                video.addEventListener('loadedmetadata', () => {
                    console.log('Video principal cargado (nativo)');
                    video.play().catch(err => {
                        console.error('Error al reproducir video principal (nativo):', err);
                    });
                }, { once: true });

                video.load();
            }
            // Otros navegadores - hls.js
            else if (Hls.isSupported()) {
                console.log('Usando hls.js para video principal');

                // Destruir instancia anterior si existe
                if (mainVideoHlsInstance) {
                    console.log('Destruyendo instancia HLS anterior del video principal');
                    mainVideoHlsInstance.destroy();
                    mainVideoHlsInstance = null;
                }

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

                mainVideoHlsInstance = hls;

                hls.on(Hls.Events.MANIFEST_PARSED, () => {
                    console.log('Manifest parseado para video principal');
                    video.play().catch(err => {
                        console.error('Error al reproducir video principal:', err);
                    });
                });

                hls.on(Hls.Events.ERROR, (event, data) => {
                    console.error('Error HLS en video principal:', data);
                    if (data.fatal) {
                        switch(data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                console.log('Recuperando de error de red (video principal)...');
                                hls.startLoad();
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                console.log('Recuperando de error de media (video principal)...');
                                hls.recoverMediaError();
                                break;
                            default:
                                console.log('Error fatal en video principal, destruyendo HLS...');
                                hls.destroy();
                                mainVideoHlsInstance = null;
                                break;
                        }
                    }
                });
            } else {
                console.warn('HLS no soportado en este navegador');
            }
        }

        // Inicializar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM loaded, inicializando video principal...');
                setTimeout(() => {
                    if (document.querySelector('.main-hls-video')) {
                        window.initMainVideoHLS();
                    }
                }, 100);
            });
        } else {
            console.log('DOM ya cargado, inicializando video principal...');
            setTimeout(() => {
                if (document.querySelector('.main-hls-video')) {
                    window.initMainVideoHLS();
                }
            }, 100);
        }

        // Limpiar al cambiar de pregunta/video
        Livewire.on('video-changed', () => {
            console.log('Video cambiado, limpiando instancia anterior...');

            if (mainVideoHlsInstance) {
                mainVideoHlsInstance.destroy();
                mainVideoHlsInstance = null;
            }

            // Resetear atributo de inicialización
            const video = document.querySelector('.main-hls-video');
            if (video) {
                video.removeAttribute('data-hls-initialized');
            }

            // Reinicializar
            setTimeout(() => {
                console.log('Reinicializando video principal después de cambio...');
                window.initMainVideoHLS();
            }, 200);
        });
    </script>
@endpush