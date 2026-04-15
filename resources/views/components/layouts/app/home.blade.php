<!DOCTYPE html>
<html class="min-h-screen" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="description" content="Apprends la langue des signes de Belgique francophone (LSFB) de manière interactive et ludique. Vidéos, exercices, dictionnaire et jeux.">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title"       content="Lsfbgo — Apprends la LSFB">
    <meta property="og:description"  content="Plateforme interactive pour apprendre la langue des signes de Belgique francophone.">
    <meta property="og:image"       content="{{ asset('img/meta/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:locale"       content="fr_BE">

    {{-- Twitter --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="Lsfbgo — Apprends la LSFB">
    <meta name="twitter:description" content="Plateforme interactive pour apprendre la langue des signes de Belgique francophone.">
    <meta name="twitter:image"       content="{{ asset('img/meta/og-image.jpg') }}">


    {{-- ✅ Iconos --}}
    <link rel="icon"             href="{{ asset('img/meta/favicon.png') }}"         type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('img/meta/apple-touch-icon.png') }}" sizes="180x180">
    <link rel="manifest"         href="{{ asset('manifest.json') }}">
    <meta name="theme-color"     content="#0099cc">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.css" />
    {{-- Favicon --}}
    <link rel="icon"             href="{{ asset('img/meta/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('img/meta/apple-touch-icon.png') }}" sizes="180x180">
    <title>Lsfbgo</title>
    @vite('resources/css/app.css')
    @fluxAppearance
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen dark:bg-slate-900 text-white overflow-x-hidden">

<header
        class="border-b border-white/10 bg-slate-900 sticky top-0 z-50"
        x-data="{ open: false }"
>
    {{-- Barra principal --}}
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 md:px-6 py-4 dark:bg-slate-900">

        <div class="flex items-center gap-3">
            <h1 class="text-lg font-bold leading-none">LSFBGO</h1>
            <p class="text-sm text-white/60 hidden sm:block">Plateforme d'apprentissage</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Avatar --}}
            <a href="{{ route('profile.edit') }}"
               class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 font-bold hover:bg-white/20 transition">
                {{ strtoupper(substr(session('data.user.name', 'U'), 0, 2)) }}
            </a>

            {{-- Hamburger — solo móvil --}}
            <button
                    class="md:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-white/10 transition"
                    @click="open = !open"
                    :aria-expanded="open.toString()"
                    aria-label="Menu de navigation"
            >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          :d="open ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"
                    />
                </svg>
            </button>
        </div>
    </div>

    {{-- Nav desktop — siempre visible en md+ --}}
    <div class="hidden md:block mx-auto max-w-7xl px-6 pb-4">
        <nav class="flex flex-wrap gap-2" x-data="navActive()">
            @foreach([
                ['route' => route('access.dashboard'), 'label' => 'Accueil'],
                ['route' => route('dictionary'),        'label' => 'Dictionnaire'],
                ['route' => route('syllabus'),          'label' => 'Syllabus'],
                ['route' => route('practice'),          'label' => 'Exercices'],
                ['route' => route('profile.edit'),      'label' => 'Profil'],
            ] as $link)
                <a wire:navigate href="{{ $link['route'] }}"
                   :class="isActive('{{ $link['route'] }}') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10'"
                   class="rounded-xl px-4 py-2 text-sm font-medium transition">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ✅ Nav móvil — solo visible cuando open=true --}}
    <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden border-t border-white/10"
    >
        <nav class="flex flex-col px-4 py-3 gap-1" x-data="navActive()">
            @foreach([
                ['route' => route('access.dashboard'), 'label' => 'Accueil',      'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => route('dictionary'),       'label' => 'Dictionnaire', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['route' => route('syllabus'),         'label' => 'Syllabus',     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['route' => route('practice'),         'label' => 'Exercices',    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['route' => route('profile.edit'),     'label' => 'Profil',       'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ] as $link)
            <a
                wire:navigate
                href="{{ $link['route'] }}"
                @click="open = false"
                :class="isActive('{{ $link['route'] }}') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10'"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition"
                >
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

</header>

<main class="mx-auto max-w-7xl px-4 md:px-6 py-6 md:py-8">
    {{ $slot }}
</main>

@vite('resources/js/app.js')
@fluxScripts
@livewireScripts
@stack('scripts')
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-DM2RRCY0TH"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-DM2RRCY0TH');
</script>
<script>
    function navActive() {
        return {
            currentPath: window.location.pathname,
            isActive(url) {
                const path = new URL(url).pathname;
                return this.currentPath === path || this.currentPath.startsWith(path + '/');
            },
            init() {
                document.addEventListener('livewire:navigated', () => {
                    this.currentPath = window.location.pathname;
                });
            }
        }
    }
</script>
</body>
</html>