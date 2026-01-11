{{-- Validate Button --}}
@if(!$answered && $currentQuestion['type'] !== 'video-choice' && $currentQuestion['type'] !== 'match')
    <div class="flex justify-center mt-4">
        <flux:button
                variant="primary"
                color="sky"
                class="w-32 cursor-pointer {{ $answered || (!$selectedAnswer && empty($userInput)) ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"
                wire:click="checkAnswer">
            Valider
        </flux:button>
    </div>
@endif

{{-- Next Button con animación de entrada --}}
@if($currentQuestion['type'] !== 'match' && $answered)
    <div x-data="{ show: false }"
         x-init="setTimeout(() => show = true, 200)"
         x-show="show"
         x-transition:enter="transform transition ease-out duration-700"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100">
        <x-sign-video-next-button :nextStep="'nextStep'" />
    </div>
@endif

{{-- Match type specific buttons --}}
@if($answered && $currentQuestion['type'] === 'match')
    <div class="mt-4 text-center"
         x-data="{ show: false }"
         x-init="setTimeout(() => show = true, 200)"
         x-show="show"
         x-transition:enter="transform transition ease-out duration-700"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100">
        <x-sign-video-next-button :nextStep="'nextStep'" />
    </div>
@endif
