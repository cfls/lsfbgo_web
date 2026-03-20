<div class="relative mb-6 w-full">
    <div class="p-2 inline-block">
        <a wire:navigate href="/" class="text-black mb-4 dark:text-white inline-flex items-center gap-2">
            <flux:icon.arrow-left-circle class="size-8"/>
            @include('partials.quiz.svg.logo', ['class' => 'w-12 h-12'])
        </a>
    </div>
    <flux:heading size="xl" level="1">{{ __('Paramètres') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Gérez les paramètres de votre profil et de votre compte') }}</flux:subheading>
    <flux:separator variant="subtle" />
</div>