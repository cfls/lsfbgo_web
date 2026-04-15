@php
    function textColorForBg(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    $bgColor   = $this->color ?? '#000000';
    $textColor = textColorForBg($bgColor);
@endphp

<div
        class="min-h-screen bg-gray-50 dark:bg-zinc-900"
        x-data="{
        bg: '{{ $bgColor }}',
        get text() {
            const hex = this.bg.replace('#','');
            const h = hex.length === 3
                ? hex.split('').map(c => c+c).join('')
                : hex;
            const r = parseInt(h.slice(0,2),16)/255;
            const g = parseInt(h.slice(2,4),16)/255;
            const b = parseInt(h.slice(4,6),16)/255;
            const l = 0.2126*r + 0.7152*g + 0.0722*b;
            return l > 0.5 ? '#000000' : '#ffffff';
        }
    }"
        @color-updated.window="bg = $event.detail.color"
>
    {{-- Header sticky --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] md:pt-0 sticky top-0 z-10 shadow-md">
        <div class="max-w-2xl mx-auto px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center gap-3">
                <span aria-hidden="true">
                    @include('partials.quiz.svg.logo', ['class' => 'w-12 h-12 md:w-16 md:h-16 shrink-0'])
                </span>
                <flux:subheading as="h1" size="xl" class="text-white text-base md:text-xl font-semibold leading-tight truncate">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 md:px-6 py-5 md:py-8 space-y-4">

        <flux:select
                wire:model.live="selectedSyllabus"
                placeholder="Choisissez un thème..."
        >
            @foreach($this->themes as $theme)
                <flux:select.option value="{{ $theme['attributes']['slug'] }}">
                    {{ strtoupper($theme['attributes']['title']) }}
                </flux:select.option>
            @endforeach
        </flux:select>

        {{-- Botones con loading --}}
        <div class="space-y-3">

            {{-- ✅ target es la propiedad, no el método --}}
            {{-- ✅ Loading con logo animado --}}
            {{-- ✅ Loading con logo animado --}}
            <div wire:loading wire:target="selectedSyllabus" class="w-full">
                <div class="flex flex-col items-center justify-center w-full py-8 gap-4">

                    {{-- Logo pulsando --}}
                    <div class="animate-pulse">
                        @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20 opacity-70'])
                    </div>

                    {{-- Texto animado con puntos --}}
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Chargement
                        <span class="inline-flex gap-0.5 ml-0.5">
                            <span class="animate-bounce [animation-delay:0ms]">.</span>
                            <span class="animate-bounce [animation-delay:150ms]">.</span>
                            <span class="animate-bounce [animation-delay:300ms]">.</span>
                        </span>
                    </p>

                    {{-- Barra de progreso --}}
                    <div class="w-40 h-1.5 bg-gray-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-teal-500 to-purple-600 rounded-full animate-[loading_1s_ease-in-out_infinite]"></div>
                    </div>

                </div>
            </div>

            <div wire:loading.remove wire:target="selectedSyllabus" class="space-y-3">
                @foreach([
                    ['type' => 'text',   'label' => '🎥✍️ Traduis en français'],
                    ['type' => 'choice', 'label' => '🎥📝 Choisir le bon mot'],
                ] as $item)
                   <a
                    wire:navigate
                    href="{{ route('questions', ['ue' => $ue, 'type' => $item['type']]) }}"
                    :style="`background-color: ${bg}; transition: background-color 0.3s ease;`"
                    class="flex items-center justify-between w-full px-5 py-4 rounded-xl shadow-sm active:scale-[0.98] hover:brightness-95 hover:shadow-md"
                    >
                    <span class="text-base md:text-lg font-semibold" :style="`color: ${text};`">
                {{ $item['label'] }}
            </span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72.4 72.4"
                         class="w-6 h-6 md:w-7 md:h-7 shrink-0" aria-hidden="true">
                        <circle :fill="text" cx="36.2" cy="36.2" r="36.2"/>
                        <polygon :fill="bg" points="12.6 28.3 37.8 28.3 37.8 12.6 61.4 36.2 37.8 59.8 37.8 44.1 12.6 44.1 12.6 28.3"/>
                    </svg>
                    </a>
                @endforeach
            </div>

        </div>

    </div>
</div>
@push('styles')
    <style>
        @keyframes loading {
            0%   { width: 0%;   margin-left: 0%; }
            50%  { width: 60%;  margin-left: 20%; }
            100% { width: 0%;   margin-left: 100%; }
        }
    </style>
@endpush