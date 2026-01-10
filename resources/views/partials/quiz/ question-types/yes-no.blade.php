<div class="flex gap-4 justify-center mt-4">
    <button type="button"
            class="px-5 py-2 rounded-lg border transition-colors
                   {{ $answered ? 'pointer-events-none opacity-70' : '' }}
                   {{ $selectedAnswer === 'oui' ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
            wire:click="selectAnswer('oui')">
        Oui
    </button>

    <button type="button"
            class="px-5 py-2 rounded-lg border transition-colors
                   {{ $answered ? 'pointer-events-none opacity-70' : '' }}
                   {{ $selectedAnswer === 'non' ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
            wire:click="selectAnswer('non')">
        Non
    </button>
</div>