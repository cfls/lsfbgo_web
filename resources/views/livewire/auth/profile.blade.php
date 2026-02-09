<div
        class="space-y-4 bg-white  min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                @include('partials.quiz.svg.logo', ['class' => 'w-8 h-8'])
                <flux:subheading class="text-white text-base">
                    Mon Profil
                </flux:subheading>
            </div>
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
        <div class="flex flex-col w-full max-w-sm p-5 bg-white border border-gray-200 rounded-lg shadow-sm space-y-4">

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
               class="flex items-center justify-between w-full p-5 bg-gray-500 border border-gray-200 rounded-lg shadow-sm">
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


        {{-- Déconnexion --}}
        <form action="{{ route('access.logout') }}" method="POST" class="mt-2">
            @csrf
            <flux:button type="submit" variant="primary" color="orange" class="w-full cursor-pointer text-white">
                Déconnexion
            </flux:button>
        </form>
    </div>
    <!-- Espacio para que no lo tape el footer -->
    <div class="h-40"></div>
</div>
