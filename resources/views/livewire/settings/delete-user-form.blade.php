<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>
            {{ __('Supprimer le compte') }}
        </flux:heading>

        <flux:subheading>
            {{ __('Supprimez définitivement votre compte et toutes les données associées.') }}
        </flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button
                variant="danger"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            {{ __('Supprimer le compte') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal
            name="confirm-user-deletion"
            :show="$errors->isNotEmpty()"
            focusable
            class="max-w-lg"
    >
        <form wire:submit="deleteUser" class="space-y-6">

            <div class="space-y-3">
                <flux:heading size="lg">
                    {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
                </flux:heading>

                <div class="rounded-lg border  p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
                    {{ __('Attention : cette action est irréversible. Toutes vos données, ressources et accès seront définitivement supprimés.') }}
                </div>

                <flux:subheading>
                    {{ __('Veuillez saisir votre mot de passe pour confirmer la suppression définitive de votre compte.') }}
                </flux:subheading>
            </div>

            <flux:input
                    wire:model="password"
                    :label="__('Mot de passe')"
                    type="password"
                    autocomplete="current-password"
            />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">
                        {{ __('Annuler') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button
                        variant="danger"
                        type="submit"
                        wire:loading.attr="disabled"
                >
                    {{ __('Supprimer définitivement') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</section>