<?php


use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Dialog;

new class extends Component
{
    public function confirmBack(): void
    {
        Dialog::alert(
            'Quitter le quiz ?',
            'Votre progression sera perdue.',
            ['Continuer', 'Quitter']
        )->id('quiz-back');
    }

    #[OnNative(ButtonPressed::class)]
    public function handleButtonPressed(int $index, string $label, ?string $id = null): void
    {
        if ($id === 'quiz-back' && $label === 'Quitter') {
            $this->redirect(route('access.dashboard'), navigate: true);
        }
    }
};
?>

<div class="flex items-center justify-end absolute top-0 right-4">
    <flux:icon.x-circle wire:click="confirmBack" />
</div>