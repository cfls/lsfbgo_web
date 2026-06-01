<div class="space-y-4 bg-white dark:bg-gray-800  min-h-screen">
     <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] shrink-0">
        <div class="px-4 py-3 flex items-center gap-3">
            @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
            <flux:subheading size="xl" class="text-white text-base font-semibold">
                Mon Profil
            </flux:subheading>
        </div>
    </div>

    <div class="flex-1 flex flex-col items-center justify-start px-3 py-6 gap-4">

        {{-- Título --}}




        {{-- Mensajes de éxito o error --}}
        @if(session('success'))
            <div class="w-full max-w-sm p-3 mb-4 text-sm text-green-700 bg-green-100 border border-green-300 rounded-lg dark:bg-green-900 dark:text-green-100 dark:border-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="w-full max-w-sm p-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded-lg dark:bg-red-900 dark:text-red-100 dark:border-red-700">
                {{ session('error') }}
            </div>
        @endif
        
         {{-- Tarjeta del perfil --}}
        <div class="flex flex-col w-full max-w-sm p-5 bg-white border-2 border-gray-300 rounded-lg space-y-4">

            {{-- Nombre --}}
            <div class="flex flex-col">
                <flux:heading class="font-bold text-black">Nom:</flux:heading>
                <flux:text class="text-gray-800">
                    {{ $profile['name'] }}
                </flux:text>
            </div>

            {{-- Email --}}
            <div class="flex flex-col">
                <flux:heading class="font-bold text-black">Email:</flux:heading>
                <flux:text class="text-gray-800">
                    {{ $profile['email'] }}
                </flux:text>
            </div>

            <hr class="border border-b-gray-300 w-full mx-auto">

            {{-- Paramètres --}}
            <a href="{{ route('profile.parameters') }}"
               class="flex items-center justify-between w-full p-5 border-2 border-gray-300 rounded-lg">
                <flux:label class="text-lg font-semibold text-gray-900 ">Paramètres</flux:label>
                <svg id="Calque_1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 72.4 72.4" class="w-5 h-5">
                    <!-- Generator: Adobe Illustrator 29.1.0, SVG Export Plug-In . SVG Version: 2.1.0 Build 142)  -->
                    <defs>
                        <style>
                            .st0 {
                                fill: #2c333e;
                            }

                            .st1 {
                                fill: #fff;
                            }
                        </style>
                    </defs>
                    <circle class="st1" cx="36.2" cy="36.2" r="36.2"/>
                    <polygon class="st0" points="12.6 28.3 37.8 28.3 37.8 12.6 61.4 36.2 37.8 59.8 37.8 44.1 12.6 44.1 12.6 28.3"/>
                </svg>
            </a>


        </div>
    </div>
    <div class="flex flex-col items-center justify-center  gap-4">
        <div class="flex flex-col items-center justify-center gap-4 w-full max-w-sm px-4">

            {{-- Déconnexion --}}
            <form action="{{ route('access.logout') }}" method="POST" class="w-full">
                @csrf

                <flux:button
                        type="submit"
                        variant="primary"
                        color="orange"
                        class="w-full cursor-pointer text-white"
                >
                    Déconnexion
                </flux:button>
            </form>

            {{-- Suppression du compte --}}
            <flux:modal.trigger name="confirm-user-deletion">
                <flux:button
                        variant="danger"
                        class="w-full cursor-pointer"
                >
                    Supprimer définitivement
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
        </div>
    </div>

    <!-- Espacio para que no lo tape el footer -->
    <div class="h-40"></div>
</div>
