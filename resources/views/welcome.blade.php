
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
    <div class="flex flex-col items-center justify-center min-h-screen
            bg-gradient-to-b dark:bg-[var(--color-primary-foreground)]">

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
