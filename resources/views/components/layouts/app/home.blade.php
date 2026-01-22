
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

    <title>LSFBGO</title>

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
    <livewire:native-edge  />
@if(request()->routeIs('profile.parameters') || request()->routeIs('user-password.edit') || request()->routeIs('appearance.edit'))
    <main>
        {{ $slot }}
    </main>

@else
    <flux:main class="!p-0 overflow-x-hidden">
        {{ $slot }}
    </flux:main>
@endif

        @vite('resources/js/app.js')
        @fluxScripts
        @livewireScripts
        @stack('scripts')
    </body>
</html>
