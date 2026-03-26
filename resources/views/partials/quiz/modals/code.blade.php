<div class="fixed inset-0 flex items-center justify-center bg-black/60 z-50 p-4">

    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl w-full max-w-md p-6 text-center">

   
      

        {{-- Title --}}
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">
            Accès requis {{ strtoupper(str_replace('-themes', '', $theme)) }}
        </h2>

        {{-- Subtitle --}}
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Entrez votre code pour accéder au contenu
        </p>

        

        {{-- Input --}}
        <input
            type="text"
            wire:model.live="accessCode"
            placeholder="Ex: ABC123UE1" 
            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-zinc-800 dark:text-white text-center text-lg tracking-widest"
        />

        {{-- Success Feedback --}}
        @if (session()->has('success'))
            <p class="text-green-500 text-sm mt-2">{{ session('success') }}</p>
        @endif

        {{-- Error --}}
        @error('accessCode')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror

        {{-- Buttons --}}
        <div class="mt-6 flex gap-3">

            {{-- Cancel --}}
            <button
                wire:click="closePaymentModal"
                class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition"
            >
                Annuler
            </button>

            {{-- Submit --}}
            <button
                wire:click="validateCode"
                class="flex-1 px-4 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition"
            >
                Valider
            </button>

        </div>

        {{-- Alternative --}}
        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Pas de code ?
            <button
                wire:click="openShop"
                class="text-blue-600 dark:text-blue-400 font-medium hover:underline"
            >
                Achetez le syllabus
            </button>
        </div>

    </div>
</div>