{{-- resources/views/livewire/syllabus/theme-detail.blade.php --}}
<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">

    @push('styles')
        <style>
            @media (prefers-reduced-motion: reduce) {
                *, *::before, *::after {
                    transition-duration: 0.01ms !important;
                    animation-duration: 0.01ms !important;
                }
            }
        </style>
    @endpush

    {{-- Header sticky --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] md:pt-0  shadow-md">
        <div class="max-w-4xl mx-auto px-4 md:px-6 py-3 md:py-4 flex items-center gap-3">

           <a
            wire:navigate
            href="{{ route('syllabus') }}"
            aria-label="Retour à la liste des syllabus"
            class="text-white shrink-0 hover:opacity-80 transition"
            >
            <flux:icon.arrow-left-circle class="size-8 md:size-9" aria-hidden="true"/>
            </a>

            <span aria-hidden="true">
                @include('partials.quiz.svg.logo', ['class' => 'w-12 h-12 md:w-16 md:h-16 shrink-0'])
            </span>

            <flux:subheading as="h1" size="xl" class="text-white text-base md:text-xl font-semibold leading-tight truncate">
                {{ $title }}
            </flux:subheading>

        </div>
    </div>

    {{-- Contenido centrado --}}
    <div class="max-w-4xl mx-auto px-4 md:px-6 py-5 md:py-8">

        {{-- Grid: 2 cols móvil → 3 tablet → 4 desktop --}}
        <ul
                role="list"
                class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5 list-none p-0 m-0"
        >
            @foreach ($this->selectedTheme as $theme)
                @php
                    $attrs  = $theme['attributes'];
                    $ue     = $attrs['slug_syllabu'];
                    $slug   = $attrs['slug'];
                    $title  = $attrs['title'];
                    $image  = $attrs['image'];
                @endphp

                <li>
                   <a
                    wire:navigate
                    href="{{ route('syllabus.themes', ['ue' => $ue, 'theme' => $slug]) }}"
                    aria-label="Accéder au thème : {{ $title }}"
                    class="block active:scale-95 transition-transform duration-200 h-full"
                    >
                    <flux:card class="w-full h-full flex flex-col items-center gap-2 p-3 md:p-4 hover:shadow-lg transition-shadow rounded-xl">

                        {{-- Imagen con skeleton --}}
                        <div
                                class="w-full aspect-square relative bg-gray-200 dark:bg-zinc-700 rounded-xl animate-pulse overflow-hidden"
                                role="img"
                                aria-label="Chargement de l'image : {{ $title }}"
                        >
                            <img
                                    src="{{ $image }}"
                                    alt=""
                                    aria-hidden="true"
                                    class="w-full h-full object-cover rounded-xl opacity-0 transition-opacity duration-500"
                                    onload="
                                        this.style.opacity = '1';
                                        this.parentElement.classList.remove('animate-pulse', 'bg-gray-200');
                                        this.parentElement.removeAttribute('role');
                                        this.parentElement.removeAttribute('aria-label');
                                    "
                            />
                        </div>

                        <span class="text-xs md:text-sm font-semibold text-center text-gray-800 dark:text-white leading-tight">
                                {{ $title }}
                            </span>

                    </flux:card>
                    </a>
                </li>

            @endforeach
        </ul>

    </div>
</div>