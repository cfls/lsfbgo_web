<div class="relative h-full bg-gray-50" x-data="scannerComponent()">
     {{-- Vue principale du scanner --}}
     <div class="h-full overflow-y-auto" x-show="!@js($showWebView) && !@js($showHistory)">
          <div class="p-4 space-y-4">
               {{-- En-tête --}}
               <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">📱 Scanner QR</h1>
                    <button
                            wire:click="toggleHistory"
                            class="p-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                         </svg>
                    </button>
               </div>

               {{-- Bouton principal de scan --}}
               <button
                       wire:click="scan"
                       class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition shadow-lg active:scale-95 transform">
                    <svg class="inline-block w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                               d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Scanner le Code QR du Syllabus
               </button>

               {{-- Options --}}
               <div class="bg-white rounded-xl p-4 shadow space-y-3">
                    <h3 class="font-semibold text-gray-700 mb-2">⚙️ Options</h3>

                    <label class="flex items-center justify-between cursor-pointer">
                         <span class="text-gray-700">Scan continu</span>
                         <input type="checkbox" wire:model.live="streaming" class="sr-only peer">
                         <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>

                    <label class="flex items-center justify-between cursor-pointer">
                         <span class="text-gray-700">Ouvrir automatiquement</span>
                         <input type="checkbox" wire:model.live="autoOpenUrls" class="sr-only peer">
                         <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>

                    <select wire:model.live="requestedFormat"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                         <option value="all">📋 Tous les formats</option>
                         <option value="qr_code">🔲 QR Code uniquement</option>
                    </select>
               </div>

               {{-- Résultat du scan unique --}}
               @if($data && !$streaming)
                    <div class="bg-white rounded-xl p-5 shadow-lg border-l-4 {{ $this->isSyllabusUrl($data) ? 'border-purple-500' : 'border-blue-500' }}">
                         <div class="flex items-start justify-between mb-3">
                              <div>
                                   <div class="text-sm font-semibold {{ $this->isSyllabusUrl($data) ? 'text-purple-600' : 'text-blue-600' }} mb-1">
                                        @if($this->isSyllabusUrl($data))
                                             📚 {{ $this->detectCodeType($data, $format) }}
                                        @else
                                             🌐 {{ $this->detectCodeType($data, $format) }}
                                        @endif
                                   </div>
                                   <div class="text-xs text-gray-500">Format : {{ $format }}</div>
                              </div>
                              <button wire:click="clearScans" class="text-gray-400 hover:text-gray-600">
                                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                   </svg>
                              </button>
                         </div>

                         <div class="bg-gray-50 p-4 rounded-lg mb-4 break-all text-sm font-mono">
                              {{ $data }}
                         </div>

                         <div class="flex flex-wrap gap-2">
                              @if($this->isUrl($data))
                                   <button
                                           wire:click="openInApp('{{ $data }}')"
                                           class="flex-1 {{ $this->isSyllabusUrl($data) ? 'bg-purple-600 hover:bg-purple-700' : 'bg-green-600 hover:bg-green-700' }} text-white py-2.5 px-4 rounded-lg transition font-medium">
                                        <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ $this->isSyllabusUrl($data) ? 'Voir le Syllabus' : 'Ouvrir' }}
                                   </button>
                              @endif

                              <button
                                      wire:click="copyToClipboard('{{ $data }}')"
                                      class="flex-1 bg-gray-600 text-white py-2.5 px-4 rounded-lg hover:bg-gray-700 transition font-medium">
                                   <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                   </svg>
                                   Copier
                              </button>

                              <button
                                      wire:click="shareCode('{{ $data }}')"
                                      class="bg-blue-600 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 transition">
                                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                   </svg>
                              </button>
                         </div>
                    </div>
               @endif

               {{-- Scans continus --}}
               @if($streaming && count($scanned) > 0)
                    <div class="bg-white rounded-xl p-4 shadow">
                         <div class="flex justify-between items-center mb-4">
                              <h3 class="font-semibold text-gray-800">
                                   Codes scannés
                                   <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full ml-2">
                                {{ count($scanned) }}
                            </span>
                              </h3>
                              <button wire:click="clearScans" class="text-red-600 text-sm hover:underline font-medium">
                                   Tout effacer
                              </button>
                         </div>

                         <div class="space-y-2 max-h-96 overflow-y-auto">
                              @foreach(array_reverse($scanned) as $scan)
                                   <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 hover:border-blue-300 transition">
                                        <div class="flex justify-between items-start text-xs text-gray-500 mb-2">
                                    <span class="font-medium">
                                        @if($scan['is_syllabus']) 📚 @endif
                                         {{ $scan['type'] ?? $scan['format'] }}
                                    </span>
                                             <span>{{ \Carbon\Carbon::parse($scan['timestamp'])->format('H:i:s') }}</span>
                                        </div>
                                        <div class="text-sm break-all mb-2">{{ $scan['data'] }}</div>

                                        @if($scan['is_url'])
                                             <button
                                                     wire:click="openUrl('{{ $scan['data'] }}')"
                                                     class="text-xs {{ $scan['is_syllabus'] ? 'bg-purple-500 hover:bg-purple-600' : 'bg-blue-500 hover:bg-blue-600' }} text-white px-3 py-1 rounded transition">
                                                  {{ $scan['is_syllabus'] ? '📚 Voir le Syllabus' : 'Ouvrir' }}
                                             </button>
                                        @endif
                                   </div>
                              @endforeach
                         </div>
                    </div>
               @endif
          </div>
     </div>

     {{-- Historique --}}
     @if($showHistory)
          <div class="absolute inset-0 bg-white z-40 flex flex-col">
               <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 flex items-center justify-between shadow">
                    <div class="flex items-center space-x-3">
                         <button wire:click="toggleHistory" class="p-2 hover:bg-blue-800 rounded-full transition">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                              </svg>
                         </button>
                         <h2 class="text-xl font-bold">📜 Historique</h2>
                    </div>
                    @if(count($scanHistory) > 0)
                         <button wire:click="clearHistory" class="text-sm bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition">
                              Effacer
                         </button>
                    @endif
               </div>

               <div class="flex-1 overflow-y-auto p-4">
                    @if(count($scanHistory) > 0)
                         <div class="space-y-3">
                              @foreach($scanHistory as $index => $scan)
                                   <div class="bg-white border rounded-xl p-4 shadow-sm hover:shadow-md transition">
                                        <div class="flex justify-between items-start mb-2">
                                             <div>
                                        <span class="inline-block {{ $scan['is_syllabus'] ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }} text-xs px-2 py-1 rounded-full font-medium">
                                            @if($scan['is_syllabus']) 📚 @endif
                                             {{ $scan['type'] }}
                                        </span>
                                                  <span class="text-xs text-gray-500 ml-2">
                                            {{ \Carbon\Carbon::parse($scan['timestamp'])->locale('fr')->diffForHumans() }}
                                        </span>
                                             </div>
                                             <button wire:click="deleteFromHistory({{ $index }})" class="text-gray-400 hover:text-red-600">
                                                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                  </svg>
                                             </button>
                                        </div>

                                        <div class="bg-gray-50 p-3 rounded text-sm break-all font-mono mb-3">
                                             {{ $scan['data'] }}
                                        </div>

                                        <div class="flex gap-2">
                                             @if($scan['is_url'])
                                                  <button
                                                          wire:click="openUrl('{{ $scan['data'] }}')"
                                                          class="flex-1 {{ $scan['is_syllabus'] ? 'bg-purple-600 hover:bg-purple-700' : 'bg-green-600 hover:bg-green-700' }} text-white py-2 px-3 rounded-lg transition text-sm font-medium">
                                                       {{ $scan['is_syllabus'] ? '📚 Voir le Syllabus' : 'Ouvrir' }}
                                                  </button>
                                             @endif
                                             <button
                                                     wire:click="copyToClipboard('{{ $scan['data'] }}')"
                                                     class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition text-sm font-medium">
                                                  Copier
                                             </button>
                                        </div>
                                   </div>
                              @endforeach
                         </div>
                    @else
                         <div class="flex flex-col items-center justify-center h-full text-gray-400">
                              <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                              </svg>
                              <p class="text-lg font-medium">Aucun scan dans l'historique</p>
                         </div>
                    @endif
               </div>
          </div>
     @endif

     {{-- WebView pour Syllabus avec Vidéos --}}
     @if($showWebView && $webViewUrl)
          <div class="absolute inset-0 bg-white z-50 flex flex-col" x-show="@js($showWebView)">
               {{-- Barre de navigation améliorée --}}
               <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-lg">
                    {{-- Ligne supérieure avec contrôles --}}
                    <div class="p-3 flex items-center space-x-2">
                         <button
                                 wire:click="closeWebView"
                                 class="p-2 hover:bg-purple-800 rounded-full transition">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                              </svg>
                         </button>

                         <button
                                 wire:click="goBack"
                                 @if(!$canGoBack) disabled @endif
                                 class="p-2 hover:bg-purple-800 rounded-full transition {{ !$canGoBack ? 'opacity-40 cursor-not-allowed' : '' }}">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                              </svg>
                         </button>

                         <button
                                 wire:click="goForward"
                                 @if(!$canGoForward) disabled @endif
                                 class="p-2 hover:bg-purple-800 rounded-full transition {{ !$canGoForward ? 'opacity-40 cursor-not-allowed' : '' }}">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                              </svg>
                         </button>

                         <div class="flex-1 bg-purple-800/50 backdrop-blur rounded-lg px-4 py-2">
                              <div class="flex items-center space-x-2">
                                   @if($isLoading)
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                             <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                             <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                   @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                   @endif
                                   <div class="flex-1">
                                        <div class="text-sm font-semibold truncate">{{ $webViewTitle }}</div>
                                        <div class="text-xs text-purple-200 truncate">{{ parse_url($webViewUrl, PHP_URL_HOST) }}</div>
                                   </div>
                              </div>
                         </div>

                         <button
                                 wire:click="reloadWebView"
                                 class="p-2 hover:bg-purple-800 rounded-full transition">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                              </svg>
                         </button>

                         <button
                                 wire:click="openInBrowser"
                                 class="p-2 hover:bg-purple-800 rounded-full transition">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                              </svg>
                         </button>
                    </div>

                    {{-- Barre de progression --}}
                    @if($isLoading)
                         <div class="h-1 bg-purple-800">
                              <div class="h-full bg-white animate-pulse" style="width: 70%"></div>
                         </div>
                    @endif
               </div>

               {{-- iFrame avec configuration optimisée pour vidéos --}}
               <div class="flex-1 overflow-hidden bg-white">
                    <iframe
                            id="syllabus-iframe"
                            src="{{ $webViewUrl }}"
                            class="w-full h-full border-0"
                            sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-presentation"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                            allowfullscreen
                            @load="
                        $wire.webViewLoaded();
                        const iframe = document.getElementById('syllabus-iframe');
                        try {
                            const canGoBack = iframe.contentWindow.history.length > 1;
                            const canGoForward = false;
                            $wire.updateNavigationState(canGoBack, canGoForward);
                        } catch(e) {
                            console.log('Cannot access iframe history');
                        }
                    "
                    ></iframe>
               </div>

               {{-- Indicateur de chargement flottant --}}
               @if($isLoading)
                    <div class="absolute inset-0 bg-white/80 flex items-center justify-center pointer-events-none">
                         <div class="bg-purple-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3">
                              <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                   <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                   <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                              </svg>
                              <span class="font-medium">Chargement du syllabus...</span>
                         </div>
                    </div>
               @endif
          </div>
     @endif
</div>

@push('scripts')
     <script>
          function scannerComponent() {
               return {
                    init() {
                         // Écouteur pour copier dans le presse-papiers
                         Livewire.on('copyToClipboard', (data) => {
                              if (navigator.clipboard) {
                                   navigator.clipboard.writeText(data.text);
                              } else {
                                   // Solution de secours pour les appareils plus anciens
                                   const textArea = document.createElement("textarea");
                                   textArea.value = data.text;
                                   document.body.appendChild(textArea);
                                   textArea.select();
                                   document.execCommand('copy');
                                   document.body.removeChild(textArea);
                              }
                         });

                         // Écouteur pour partager
                         Livewire.on('shareContent', (data) => {
                              if (navigator.share) {
                                   navigator.share({
                                        title: 'Code QR',
                                        text: data.content,
                                        url: data.content
                                   }).catch(err => console.log('Erreur de partage:', err));
                              } else {
                                   console.log('API de partage non supportée');
                              }
                         });

                         // Écouteur pour la vibration
                         Livewire.on('vibrate', (data) => {
                              if (navigator.vibrate) {
                                   navigator.vibrate(data.duration || 100);
                              }
                         });

                         // Écouteur pour recharger l'iframe
                         Livewire.on('reloadWebView', () => {
                              const iframe = document.getElementById('syllabus-iframe');
                              if (iframe) {
                                   iframe.contentWindow.location.reload();
                              }
                         });

                         // Écouteur pour la navigation iframe
                         Livewire.on('webViewGoBack', () => {
                              const iframe = document.getElementById('syllabus-iframe');
                              if (iframe && iframe.contentWindow) {
                                   try {
                                        iframe.contentWindow.history.back();
                                   } catch(e) {
                                        console.log('Impossible de naviguer en arrière');
                                   }
                              }
                         });

                         Livewire.on('webViewGoForward', () => {
                              const iframe = document.getElementById('syllabus-iframe');
                              if (iframe && iframe.contentWindow) {
                                   try {
                                        iframe.contentWindow.history.forward();
                                   } catch(e) {
                                        console.log('Impossible de naviguer en avant');
                                   }
                              }
                         });

                         // Écouteur pour ouvrir dans un navigateur externe
                         Livewire.on('openExternal', (data) => {
                              window.open(data.url, '_system');
                         });

                         // Écouteur pour les notifications
                         Livewire.on('notify', (data) => {
                              // Implémenter le système de notifications
                              console.log(`[${data.type}] ${data.message}`);

                              // Optionnel : Afficher une notification toast
                              this.showToast(data.message, data.type);
                         });
                    },

                    showToast(message, type = 'success') {
                         // Créer un élément de notification
                         const toast = document.createElement('div');
                         toast.className = `fixed top-4 right-4 z-[9999] px-6 py-3 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300 ${
                                 type === 'success' ? 'bg-green-600' :
                                         type === 'error' ? 'bg-red-600' :
                                                 'bg-blue-600'
                         }`;
                         toast.textContent = message;
                         toast.style.opacity = '0';
                         toast.style.transform = 'translateY(-20px)';

                         document.body.appendChild(toast);

                         // Animer l'entrée
                         setTimeout(() => {
                              toast.style.opacity = '1';
                              toast.style.transform = 'translateY(0)';
                         }, 10);

                         // Supprimer après 3 secondes
                         setTimeout(() => {
                              toast.style.opacity = '0';
                              toast.style.transform = 'translateY(-20px)';
                              setTimeout(() => toast.remove(), 300);
                         }, 3000);
                    }
               }
          }
     </script>
@endpush

@push('styles')
     <style>
          /* Assurer que l'iframe permette les vidéos en plein écran */
          #syllabus-iframe {
               position: relative;
               z-index: 1;
          }

          /* Styles pour la barre de défilement personnalisée */
          .overflow-y-auto::-webkit-scrollbar {
               width: 6px;
          }

          .overflow-y-auto::-webkit-scrollbar-track {
               background: #f1f1f1;
               border-radius: 10px;
          }

          .overflow-y-auto::-webkit-scrollbar-thumb {
               background: #888;
               border-radius: 10px;
          }

          .overflow-y-auto::-webkit-scrollbar-thumb:hover {
               background: #555;
          }

          /* Animation de chargement */
          @keyframes pulse {
               0%, 100% { opacity: 1; }
               50% { opacity: 0.5; }
          }
     </style>
@endpush