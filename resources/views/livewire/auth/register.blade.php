    <div class="flex flex-col gap-6">
        <x-auth-header title="Créer un compte" description="Entrez vos informations ci-dessous pour créer votre compte" />

        <!-- Statut de la session -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($error)
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-center dark:border-red-800/50 dark:bg-red-950/50">
                <div class="flex items-center justify-center gap-2">
                    <svg class="size-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-red-800 dark:text-red-200">{{ $error }}</span>
                </div>
            </div>
        @endif

        <form wire:submit="register" class="flex flex-col gap-6">
            <!-- Nom -->
            <flux:input
                    wire:model="name"
                    label="Nom"
                    type="text"
                    autofocus
                    autocomplete="name"
                    placeholder="Nom complet"
            />

            <!-- Adresse e-mail -->
            <flux:input
                    wire:model="email"
                    label="Adresse e-mail"
                    type="email"
                    autocomplete="email"
                    placeholder="email@exemple.com"
            />

            <!-- Mot de passe -->
            <flux:input
                    wire:model="password"
                    label="Mot de passe"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Mot de passe"
                    viewable
            />

            <!-- Confirmer le mot de passe -->
            <flux:input
                    wire:model="password_confirmation"
                    label="Confirmer le mot de passe"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Confirmer le mot de passe"
                    viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" color="orange" class="w-full">
                    Créer un compte
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>Vous avez déjà un compte ?</span>
            <flux:link :href="route('access.login')">Se connecter</flux:link>
        </div>
    </div>


