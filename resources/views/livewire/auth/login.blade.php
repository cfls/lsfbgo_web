
    <div class="flex flex-col gap-6">

        <x-auth-header :title="__('Connectez-vous à votre compte')" :description="__('Entrez votre email et mot de passe ci-dessous pour vous connecter')" />

        <!-- Session Status -->
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
            @if (Str::contains($error, 'not verified'))
                <form wire:submit="sendCode">
                    <div class="flex justify-center-center justify-center mt-3">
                        <flux:button  type="submit" variant="primary" class="w-full">
                            Renvoyer le code
                        </flux:button>
                    </div>
                </form>
            @endif
        @endif


        <form wire:submit="login" class="flex flex-col gap-6">
            <!-- Adresse e-mail -->
            <flux:input
                    wire:model="email"
                    label="Adresse e-mail"
                    type="email"
                    autofocus
                    autocomplete="off"
                    placeholder="email@exemple.com"
            />

            <!-- Mot de passe -->
            <div class="relative">
                <flux:input
                        wire:model="password"
                        label="Mot de passe"
                        type="password"
                        autocomplete="off"
                        placeholder="Mot de passe"
                        viewable
                />

                @if (Route::has('pass.request'))
                    <flux:link class="absolute end-0 top-0 text-sm" :href="route('pass.request')" >
                        Mot de passe oublié ?
                    </flux:link>
                @endif
            </div>

            <!-- Se souvenir de moi -->
            <flux:checkbox wire:model="remember" label="Se souvenir de moi" class="bg-white" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" color="orange" type="submit" class="w-full text-white">Se connecter</flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('access.register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>

