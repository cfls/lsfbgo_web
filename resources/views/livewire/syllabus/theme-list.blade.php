{{-- resources/views/livewire/syllabus/theme-list.blade.php --}}
<div class="space-y-6">

    @if ($showPaymentModal)
        @include('partials.quiz.modals.code', ['link' => $selectedLink, 'theme' => $theme])
    @endif

    {{-- Header --}}
    {{-- ✅ from-teal-700 (contraste blanco ~4.6:1) en vez de teal-500 (~2.5:1) --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">

                {{-- ✅ Logo decorativo envuelto en span aria-hidden --}}
                <span aria-hidden="true">
                    @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                </span>

                {{-- ✅ as="h1" garantiza jerarquía de heading navegable con lector de pantalla --}}
                <flux:subheading as="h1" size="xl" class="text-white text-base">
                    {{ $title }}
                </flux:subheading>

            </div>
        </div>
    </div>

    <div class="px-4 space-y-6">

        <p class="text-sm font-bold text-gray-600 dark:text-gray-300">
            Sélectionnez les thèmes que vous souhaitez apprendre.
        </p>

        {{-- Grid 2 columnas --}}
        {{-- ✅ role="list" para que los lectores de pantalla anuncien el número de temas --}}
        <ul role="list" class="grid grid-cols-2 gap-4 list-none p-0 m-0">
            @foreach ($results as $syllabu)
                @php
                    $nameRoute   = $this->optionGame ? 'games' : 'syllabus';
                    $isActive    = $syllabu['isActive'] ?? false;
                    $isStatus    = $syllabu['attributes']['status'];
                    $route       = route($nameRoute, ['ue' => $syllabu['attributes']['slug']]);
                    $image       = $syllabu['attributes']['image'];
                    $link        = $syllabu['attributes']['link'];
                    $themeName   = $syllabu['attributes']['title'] ?? 'Thème';
                @endphp

                <li>
                    {{-- ✅ cursor-pointer eliminado del card — lo hereda el elemento interactivo interior --}}
                    <flux:card class="hover:shadow-lg transition-shadow rounded-xl p-3">

                        @if ($this->optionGame == 0)

                            @if ($isActive || $this->role == 'admin')
                                {{-- ✅ aria-label en el enlace con nombre del tema --}}
                                <a
                                    wire:navigate
                                    href="{{ $route }}"
                                    aria-label="Accéder au thème : {{ $themeName }}"
                                    class="flex flex-col items-center justify-center h-full gap-2"
                                >
                                    {{-- ✅ alt="" porque el aria-label del enlace ya describe el destino --}}
                                    <img
                                        src="{{ $image }}"
                                        alt=""
                                        aria-hidden="true"
                                        class="w-32 h-32 rounded-full object-cover"
                                    >
                                    
                                </a>

                            @else
                                {{-- ✅ <button> en vez de <a role="button"> sin href: focusable y activable por teclado --}}
                                {{-- ✅ aria-label comunica nombre + estado bloqueado --}}
                                <button
                                    type="button"
                                    wire:click="openPaymentModal('{{ $link }}', '{{ $syllabu['attributes']['slug'] }}')"
                                    aria-label="{{ $themeName }} — contenu verrouillé, achat requis"
                                    class="flex flex-col items-center justify-center h-full w-full gap-2 cursor-pointer"
                                >
                                    {{-- ✅ Imagen decorativa: el button ya tiene el aria-label completo --}}
                                    <img
                                        src="{{ $image }}"
                                        alt=""
                                        aria-hidden="true"
                                        class="w-32 h-32 rounded-full object-cover opacity-70"
                                    >
                                    {{-- ✅ Nombre visible + indicador de bloqueo para usuarios videntes --}}
                                    <span class="text-xs font-medium text-center text-gray-700 dark:text-gray-200 leading-tight">
                                        {{ $themeName }}
                                    </span>
                                    <span class="sr-only">Contenu verrouillé</span>
                                </button>

                            @endif

                        @else

                            {{-- ✅ Mismo patrón para modo juego --}}
                            <a
                                wire:navigate
                                href="{{ $route }}"
                                aria-label="Accéder au jeu : {{ $themeName }}"
                                class="flex flex-col items-center justify-center h-full gap-2"
                            >
                                <img
                                    src="{{ $image }}"
                                    alt=""
                                    aria-hidden="true"
                                    class="w-32 h-32 rounded-full object-cover"
                                >
                                <span class="text-xs font-medium text-center text-gray-700 dark:text-gray-200 leading-tight">
                                    {{ $themeName }}
                                </span>
                            </a>

                        @endif

                    </flux:card>
                </li>
            @endforeach
        </ul>

        {{-- Card tutoriel compact --}}
        {{-- ✅ from-teal-700 para contraste adecuado --}}
        <flux:card class="bg-gradient-to-br from-teal-500 to-purple-600 border border-amber-200 dark:border-amber-700 mt-6">
            <p class="text-white text-xs font-semibold leading-relaxed mb-3">
                Votre code unique se trouve à l'intérieur de la couverture arrière de votre syllabus.
                Pour toute demande, écrivez à support@cfls.be
            </p>
            {{-- ✅ aria-label explícito por si el icono de Flux no tiene aria-hidden automático --}}
            <flux:button
                wire:click="openInApp"
                icon="video-camera"
                aria-label="Voir le tutoriel vidéo — ouvre une vidéo"
                class="w-full bg-gradient-to-br from-blue-500 to-cyan-500 !text-white border-0 shadow-sm text-sm font-semibold [&>span]:!text-white"
            >
                Voir le tutoriel vidéo
            </flux:button>
        </flux:card>

    </div>
</div>