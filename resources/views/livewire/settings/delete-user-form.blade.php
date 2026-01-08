<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Supprimer le compte') }}</flux:heading>
        <flux:subheading>{{ __('Supprimez votre compte et toutes ses ressources') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            {{ __('Supprimer le compte') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Veuillez saisir votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Mot de passe')" type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Annuler') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Supprimer le compte') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>