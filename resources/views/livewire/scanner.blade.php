<div class="p-4">
     <!-- Scanner Controls -->
     <div class="space-y-4">
          <div class="flex items-center space-x-4">
               <label class="flex items-center space-x-2">
                    <input
                            type="checkbox"
                            wire:model.live="streaming"
                            class="rounded"
                    >
                    <span>Continuous Scanning hh</span>
               </label>
          </div>

          <div>
               <label class="block mb-2">Format</label>
               <select wire:model.live="requestedFormat" class="w-full p-2 border rounded">
                    <option value="all">All Formats</option>
                    <option value="qr_code">QR Code</option>
                    <option value="ean_13">EAN-13</option>
                    <option value="code_128">Code 128</option>
               </select>
          </div>

          <button
                  wire:click="scan"
                  class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold"
          >
               Start Scanning
          </button>

          @if($data)
               <div class="p-4 bg-gray-100 rounded-lg">
                    <h3 class="font-semibold mb-2">Last Scanned:</h3>
                    <p class="text-sm break-all"><strong>Data:</strong> {{ $data }}</p>
                    <p class="text-sm"><strong>Format:</strong> {{ $format }}</p>

                    @if(filter_var($data, FILTER_VALIDATE_URL))
                         <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded">
                              <p class="text-xs text-green-700">✓ URL detected - Navigating automatically...</p>
                         </div>
                    @endif
               </div>
          @endif

          @if($streaming && count($scanned) > 0)
               <div class="space-y-2">
                    <div class="flex justify-between items-center">
                         <h3 class="font-semibold">Scanned Items ({{ count($scanned) }})</h3>
                         <button
                                 wire:click="clearScans"
                                 class="text-sm text-red-600"
                         >
                              Clear All
                         </button>
                    </div>

                    <div class="max-h-96 overflow-y-auto space-y-2">
                         @foreach($scanned as $index => $scan)
                              <div class="p-3 bg-gray-50 rounded border">
                                   <p class="text-xs text-gray-500">{{ $scan['timestamp'] }}</p>
                                   <p class="text-sm break-all"><strong>Data:</strong> {{ $scan['data'] }}</p>
                                   <p class="text-sm"><strong>Format:</strong> {{ $scan['format'] }}</p>

                                   @if(filter_var($scan['data'], FILTER_VALIDATE_URL))
                                        <button
                                                wire:click="navigateToScanned({{ $index }})"
                                                class="mt-2 text-sm bg-blue-600 text-white px-3 py-1 rounded"
                                        >
                                             Open
                                        </button>
                                   @endif
                              </div>
                         @endforeach
                    </div>
               </div>
          @endif
     </div>
</div>