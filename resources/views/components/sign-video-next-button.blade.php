@props(['nextStep'])
<div class="flex justify-center mt-4">
    <flux:button
            variant="primary"
            color="green"
            wire:click="nextStep"
    >
        Suivant →
    </flux:button>
</div>