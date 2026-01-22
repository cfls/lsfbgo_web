<div class="p-4">
     <!-- Header with Logo -->
     <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl shadow-lg mb-4">
               @include('partials.quiz.svg.logo')
          </div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Scanner QR</h1>
          <p class="text-sm text-gray-500 mt-1 dark:text-white">Scannez les codes QR instantanément</p>
     </div>

     <!-- Hidden Format Input (QR Code only) -->
     <input type="hidden" wire:model="requestedFormat" value="qr_code">

     <!-- Scan Button -->
     <div class="mb-6">
          <flux:button
                  variant="primary"
                   wire:click="scan"
                  class="w-full  py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center justify-center space-x-3 mt-5"

          ><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
               </svg>
          </flux:button>

     </div>

     <!-- Last Scanned Result -->
     @if($data)
          <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-md border border-gray-200">
               <h3 class="font-semibold mb-3 text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Dernier scan :
               </h3>
               <p class="text-sm break-all mb-1"><strong>Données :</strong> {{ $data }}</p>
               <p class="text-sm text-gray-600"><strong>Format :</strong> {{ $format }}</p>

               @if(filter_var($data, FILTER_VALIDATE_URL))
                    <div class="mt-3 p-3 bg-green-50 border border-green-300 rounded-lg">
                         <p class="text-xs text-green-700 flex items-center">
                              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                              </svg>
                              URL détectée - Navigation automatique...
                         </p>
                    </div>
               @endif
          </div>
     @endif

     <!-- Scanned Items List (if streaming mode is active) -->
     @if($streaming && count($scanned) > 0)
          <div class="space-y-3 mt-6">
               <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-gray-700">Éléments scannés ({{ count($scanned) }})</h3>
                    <button
                            wire:click="clearScans"
                            class="text-sm text-red-600 hover:text-red-700 font-medium"
                    >
                         Tout effacer
                    </button>
               </div>

               <div class="max-h-96 overflow-y-auto space-y-2">
                    @foreach($scanned as $index => $scan)
                         <div class="p-3 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                              <p class="text-xs text-gray-500 mb-1">{{ $scan['timestamp'] }}</p>
                              <p class="text-sm break-all mb-1"><strong>Données :</strong> {{ $scan['data'] }}</p>
                              <p class="text-sm text-gray-600"><strong>Format :</strong> {{ $scan['format'] }}</p>

                              @if(filter_var($scan['data'], FILTER_VALIDATE_URL))
                                   <button
                                           wire:click="navigateToScanned({{ $index }})"
                                           class="mt-2 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg transition-colors"
                                   >
                                        Ouvrir
                                   </button>
                              @endif
                         </div>
                    @endforeach
               </div>
          </div>
     @endif
</div>