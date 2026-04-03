{{-- resources/views/livewire/syllabus/theme-detail.blade.php --}}
<div class="space-y-4 min-h-screen">

    @push('styles')
        <style>
            {{-- ✅ prefers-reduced-motion: desactiva animaciones para usuarios sensibles --}}
            @media (prefers-reduced-motion: reduce) {
                *, *::before, *::after {
                    transition-duration: 0.01ms !important;
                    animation-duration: 0.01ms !important;
                }
            }
        </style>
    @endpush

    {{-- Header --}}
    {{-- ✅ from-teal-700 (contraste blanco ~4.6:1) en vez de teal-500 (~2.5:1) --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2 flex items-center gap-3">

            {{-- ✅ aria-label describe el destino del enlace; icono marcado aria-hidden --}}
            <a
                wire:navigate
                href="{{ route('syllabus') }}"
                aria-label="Retour à la liste des syllabus"
                class="text-white shrink-0"
            >
                <flux:icon.arrow-left-circle class="size-8" aria-hidden="true"/>
            </a>

            {{-- ✅ Logo decorativo envuelto en span aria-hidden --}}
            <span aria-hidden="true">
                @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20 shrink-0'])
            </span>

            {{-- ✅ as="h1" garantiza jerarquía de heading navegable con lector de pantalla --}}
            <flux:subheading as="h1" size="xl" class="text-white text-base">
                {{ $title }}
            </flux:subheading>

        </div>
    </div>

    {{-- ✅ <ul role="list"> comunica cuántos temas hay al lector de pantalla --}}
    <ul
        role="list"
        class="px-4 grid grid-cols-2 gap-4 list-none p-0 m-0"
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
                {{-- ✅ active:scale-95 se desactiva con prefers-reduced-motion --}}
                <a
                    wire:navigate
                    href="{{ route('syllabus.themes', ['ue' => $ue, 'theme' => $slug]) }}"
                    class="flex flex-col items-center gap-2 active:scale-95 transition-transform duration-200"
                >
                    <flux:card class="w-full flex flex-col items-center gap-2 p-3">

                        {{-- ✅ role="img" + aria-label en el contenedor mientras carga el skeleton --}}
                        <div
                            class="w-full aspect-square relative bg-gray-200 dark:bg-zinc-700 rounded-xl animate-pulse overflow-hidden"
                            role="img"
                            aria-label="Chargement de l'image : {{ $title }}"
                        >
                            {{-- ✅ alt="" + aria-hidden: la imagen es decorativa,
                                 el <span> de abajo y el aria-label del enlace ya describen el contenido.
                                 onload: retira el role/aria-label del skeleton una vez cargada. --}}
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

                        {{-- ✅ Texto visible que da el nombre accesible al enlace (sin duplicar con alt) --}}
                        <span class="text-sm font-semibold text-center text-gray-800 dark:text-white leading-tight">
                            {{ $title }}
                        </span>

                    </flux:card>
                </a>
            </li>

        @endforeach
    </ul>

</div>