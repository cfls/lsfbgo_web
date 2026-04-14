<!DOCTYPE html>
<html class="min-h-screen" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, viewport-fit=cover">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.css" />

    <title>LSFBGO</title>

    @vite('resources/css/app.css')
    @fluxAppearance
    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-900 text-white overflow-x-hidden">

<header class="border-b border-white/10 bg-slate-900">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-lg font-bold leading-none">LSFBGO</h1>
                <p class="text-sm text-white/60">Plateforme d'apprentissage</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
{{--            <div class="text-right">--}}
{{--                <p class="text-sm font-semibold">{{ session('data.user.name', 'Usuario') }}</p>--}}
{{--                <p class="text-xs text-white/60">{{ session('data.user.email', '') }}</p>--}}
{{--            </div>--}}

            <a href="{{ route('profile.edit') }}"
               class="flex h-10 w-10 items-center justify-center rounded-full font-bold">
                {{ strtoupper(substr(session('data.user.name', 'U'), 0, 2)) }}
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 pb-4">
        <nav class="flex flex-wrap gap-2">
            <a wire:navigate  href="{{ route('access.dashboard') }}"
               class="rounded-xl px-4 py-2 text-sm font-medium hover:bg-white/10 transition">
                Accueil
            </a>

            <a wire:navigate href="{{ route('dictionary') }}"
               class="rounded-xl px-4 py-2 text-sm font-medium hover:bg-white/10 transition">
                Dictionnaire
            </a>

            <a wire:navigate href="{{ route('syllabus') }}"
               class="rounded-xl px-4 py-2 text-sm font-medium hover:bg-white/10 transition">
                Syllabus
            </a>

            <a wire:navigate href="{{ route('practice') }}"
               class="rounded-xl px-4 py-2 text-sm font-medium hover:bg-white/10 transition">
                Exercices
            </a>

            <a wire:navigate href="{{ route('profile.edit') }}"
               class="rounded-xl px-4 py-2 text-sm font-medium bg-white/10 hover:bg-white/20 transition">
                Profil
            </a>
        </nav>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-8">
    {{ $slot }}
</main>

@vite('resources/js/app.js')
@fluxScripts
@livewireScripts
@stack('scripts')
</body>
</html>