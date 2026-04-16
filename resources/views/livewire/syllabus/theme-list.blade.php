{{-- resources/views/livewire/syllabus/theme-list.blade.php --}}
<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">

    @if ($showPaymentModal)
        @include('partials.quiz.modals.code', ['link' => $selectedLink, 'theme' => $theme])
    @endif

    {{-- Header --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] md:pt-0  shadow-md">
        <div class="max-w-4xl mx-auto px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center gap-3">
                <span aria-hidden="true">
                    @include('partials.quiz.svg.logo', ['class' => 'w-14 h-14 md:w-16 md:h-16 flex-shrink-0'])
                </span>
                <flux:subheading as="h1" size="xl" class="text-white text-base md:text-xl font-semibold leading-tight">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>

    {{-- Contenido principal centrado en desktop --}}
    <div class="max-w-4xl mx-auto px-4 md:px-6 py-5 md:py-8 space-y-6">

        <p class="text-sm font-bold text-gray-600 dark:text-gray-300">
            Sélectionnez les thèmes que vous souhaitez apprendre.
        </p>

        {{-- Grid: 2 cols móvil → 3 cols tablet → 4 cols desktop --}}
        <ul role="list" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5 list-none p-0 m-0">
            @foreach ($results as $syllabu)
                @php
                    $nameRoute = $this->optionGame ? 'games' : 'syllabus';
                    $isActive  = $syllabu['isActive'] ?? false;
                    $isStatus  = $syllabu['attributes']['status'];
                    $route     = route($nameRoute, ['ue' => $syllabu['attributes']['slug']]);
                    $image     = $syllabu['attributes']['image'];
                    $link      = $syllabu['attributes']['link'];
                    $themeName = $syllabu['attributes']['title'] ?? 'Thème';
                @endphp

                <li>
                    <flux:card class="hover:shadow-lg transition-shadow rounded-xl p-3 md:p-4 h-full">

                        @if ($this->optionGame == 0)

                            @if ($isActive || $this->role == 'admin')
                             <a
                                wire:navigate
                                href="{{ $route }}"
                                aria-label="Accéder au thème : {{ $themeName }}"
                                class="flex flex-col items-center justify-center h-full gap-2 md:gap-3"
                                >
                                <img
                                        src="{{ $image }}"
                                        alt=""
                                        aria-hidden="true"
                                        class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full object-cover"
                                >
                                <span class="hidden text-xs md:text-sm font-medium text-center text-gray-700 dark:text-gray-200 leading-tight">
                                        {{ $themeName }}
                                    </span>
                                </a>

                            @else
                                <button
                                        type="button"
                                        wire:click="openPaymentModal('{{ $link }}', '{{ $syllabu['attributes']['slug'] }}')"
                                        aria-label="{{ $themeName }} — contenu verrouillé, achat requis"
                                        class="flex flex-col items-center justify-center h-full w-full gap-2 md:gap-3 cursor-pointer"
                                >
                                    <div class="relative">
                                        <img
                                                src="{{ $image }}"
                                                alt=""
                                                aria-hidden="true"
                                                class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full object-cover opacity-70"
                                        >
                                        {{-- Candado visible para videntes --}}
                                        <div class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
                                            <svg class="w-8 h-8 text-white drop-shadow" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 1a5 5 0 0 0-5 5v3H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V11a2 2 0 0 0-2-2h-2V6a5 5 0 0 0-5-5zm0 2a3 3 0 0 1 3 3v3H9V6a3 3 0 0 1 3-3zm0 9a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <span class="text-xs md:text-sm font-medium text-center text-gray-700 dark:text-gray-200 leading-tight">
                                        {{ $themeName }}
                                    </span>
                                    <span class="sr-only">Contenu verrouillé</span>
                                </button>
                            @endif

                        @else

                            wire:navigate
                            href="{{ $route }}"
                            aria-label="Accéder au jeu : {{ $themeName }}"
                            class="flex flex-col items-center justify-center h-full gap-2 md:gap-3"
                            >
                            <img
                                    src="{{ $image }}"
                                    alt=""
                                    aria-hidden="true"
                                    class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full object-cover"
                            >
                            <span class="text-xs md:text-sm font-medium text-center text-gray-700 dark:text-gray-200 leading-tight">
                                    {{ $themeName }}
                                </span>
                            </a>
                        @endif

                    </flux:card>
                </li>
            @endforeach
        </ul>

        {{-- Card tutorial --}}
        <flux:card class="bg-gradient-to-br from-teal-500 to-purple-600 border border-teal-400/30 mt-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6">
                <p class="text-white text-xs md:text-sm font-medium leading-relaxed mb-3 sm:mb-0 sm:flex-1">
                    Votre code unique se trouve à l'intérieur de la couverture arrière de votre syllabus.
                    Pour toute demande, écrivez à
                    <a href="mailto:support@cfls.be" class="underline underline-offset-2 hover:opacity-80 transition">
                        support@cfls.be
                    </a>
                </p>
                <flux:button
                        x-on:click="window.open('https://www.facebook.com/share/v/1BepzAgdKA', '_blank')"
                        icon="video-camera"
                        aria-label="Voir le tutoriel vidéo — ouvre une vidéo dans un nouvel onglet"
                        class="w-full sm:w-auto bg-gradient-to-br from-blue-500 to-cyan-500 !text-white border-0 shadow-sm text-sm font-semibold [&>span]:!text-white whitespace-nowrap"
                >
                    Voir le tutoriel
                </flux:button>
            </div>
        </flux:card>

    </div>
</div>