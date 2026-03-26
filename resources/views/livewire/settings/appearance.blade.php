<section class="w-full flex items-center justify-center mx-auto px-4 py-8">
    <div class="w-full max-w-2xl border-2 border-black dark:border-white rounded-lg p-4  dark:bg-gray-900">
        @include('partials.settings-heading')

        <x-settings.layout :heading="__('Apparence')" :subheading=" __('Mettre à jour les paramètres d\'apparence de votre compte')">
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                <flux:radio value="light" icon="sun">{{ __('Clair') }}</flux:radio>
                <flux:radio value="dark" icon="moon">{{ __('Sombre') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop">{{ __('Système') }}</flux:radio>
            </flux:radio.group>
        </x-settings.layout>
    </div>
</section>