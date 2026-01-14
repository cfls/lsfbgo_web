{{-- Componente para verificar la conexión de red en tiempo real --}}
<div x-data="networkChecker()" x-init="init()" class="network-status-wrapper">
    <div x-show="!isConnected" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="fixed top-0 left-0 right-0 bg-red-500 text-white px-4 py-2 text-center z-50 shadow-lg">
        <div class="flex items-center justify-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"/>
            </svg>
            <span class="font-medium">Sin conexión a internet</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function networkChecker() {
        return {
            isConnected: true,
            checkInterval: null,

            init() {
                this.checkConnection();
                this.startChecking();
                
                // Escuchar eventos de cambio de conectividad
                window.addEventListener('online', () => this.handleOnline());
                window.addEventListener('offline', () => this.handleOffline());
            },

            async checkConnection() {
                try {
                    const response = await fetch('/api/network/status', {
                        method: 'GET',
                        cache: 'no-cache'
                    });
                    
                    if (!response.ok) {
                        this.isConnected = false;
                        return;
                    }

                    const data = await response.json();
                    this.isConnected = data.connected;

                    if (!this.isConnected) {
                        this.redirectToOfflinePage();
                    }
                } catch (error) {
                    console.error('Error checking network:', error);
                    this.isConnected = false;
                    this.redirectToOfflinePage();
                }
            },

            startChecking() {
                // Verificar cada 5 segundos
                this.checkInterval = setInterval(() => {
                    this.checkConnection();
                }, 5000);
            },

            handleOnline() {
                this.isConnected = true;
                console.log('Conexión restablecida');
            },

            handleOffline() {
                this.isConnected = false;
                this.redirectToOfflinePage();
            },

            redirectToOfflinePage() {
                if (window.location.pathname !== '/sin-conexion') {
                    window.location.href = '/sin-conexion';
                }
            },

            destroy() {
                if (this.checkInterval) {
                    clearInterval(this.checkInterval);
                }
            }
        }
    }
</script>
@endpush

<style>
    .network-status-wrapper {
        position: relative;
        z-index: 9999;
    }
</style>
