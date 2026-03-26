<div class="relative mb-6 w-full 
    bg-orange-50
    dark:bg-gray-900
    rounded-lg border border-orange-200 dark:border-orange-900/40 p-6">

    <div class="p-2 inline-block">
        <a wire:navigate href="{{ route('profile.edit') }}" class="text-orange-700 dark:text-white mb-4 inline-flex items-center gap-2">
            <flux:icon.arrow-left-circle class="size-8"/>
            @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
        </a>
    </div>

    <flux:heading size="xl" level="1" class="text-orange-900 dark:text-orange-100">
        {{ __('Paramètres') }}
    </flux:heading>
    <flux:subheading size="lg" class="mb-6 text-orange-700 dark:text-white">
        {{ __('Gérez les paramètres de votre profil et de votre compte') }}
    </flux:subheading>
    <flux:separator variant="subtle" />
</div>