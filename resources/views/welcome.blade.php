
<!DOCTYPE html>
<html class="min-h-screen" lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, viewport-fit=cover">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link
            rel="stylesheet"
            href="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.css"
    />
    <script src="https://unpkg.com/cloudinary-core/cloudinary-core-shrinkwrap.min.js"></script>
    <script src="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.js"></script>

    <title>Bienvenue LSFBGO</title>

    @vite('resources/css/app.css')
    @fluxAppearance
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles
</head>
<body class="min-h-screen dark:bg-[var(--color-primary-foreground)] overflow-x-hidden">




<flux:main class="!p-0 overflow-x-hidden">

    @if (session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md px-4">
            <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="ml-4 text-white hover:text-red-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif
    <div class="flex flex-col items-center justify-center min-h-screen
            bg-gradient-to-b dark:bg-[var(--color-primary-foreground)]">
        {{-- Mensajes Flash --}}
        @if (session('success'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="w-full max-w-md px-4 mb-5">
                <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="ml-4 text-white hover:text-green-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
        <!-- Logo / Marca -->
        <div class="flex flex-col items-center gap-2">
            @include('partials.quiz.svg.logo', ['class' => 'w-48 h-48 mb-4'])

            <p class="text-zinc-600 dark:text-zinc-300 text-center max-w-md w-1/2">
                Apprenez la langue des signes d’une manière ludique, étape par étape!
            </p>
        </div>

        <!-- Botones -->
        <div class="mt-10  max-w-sm flex flex-col gap-4 text-center">


         <a href="{{ route('access.login') }}" class="bg-orange-500 p-2 text-sm rounded-sm text-white"> Se connecter</a>
         <a href="{{ route('access.register') }}" class="bg-orange-500 text-sm p-2 rounded-sm text-white">S'inscrire gratuitement</a>

        </div>

        <!-- Footer -->
        <div class="mt-8 text-sm text-zinc-500 dark:text-zinc-400">
            <span>© {{ date('Y') }} LSFBGo </span>
        </div>
    </div>
</flux:main>


@vite('resources/js/app.js')
@fluxScripts
@livewireScripts
@stack('scripts')
</body>
</html>
