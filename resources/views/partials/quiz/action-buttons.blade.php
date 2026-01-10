{{-- Validate Button (for non video-choice and non-match types) --}}
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

{{-- Next Button (for non-match types) --}}
@if($currentQuestion['type'] !== 'match' && $answered)
    <x-sign-video-next-button :nextStep="'nextQuestion'" />
@endif

{{-- Match type specific buttons --}}
@if($answered && $currentQuestion['type'] === 'match')
    <div class="mt-4 text-center">
        <x-sign-video-next-button :nextStep="'nextQuestion'" />
    </div>
@endif