@props(['nextStep'])
<div class="flex justify-center mt-4 mb-4 px-6 w-full max-w-sm">
    <flux:button
            variant="primary"
            color="green"
            wire:click.prevent="nextStep"
            class="w-full max-w-sm"
    >
        Suivant →
    </flux:button>
</div>