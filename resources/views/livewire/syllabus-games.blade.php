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

        // Luminancia relativa
        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    $bgColor   = $this->color ?? '#ffffff';
    $textColor = textColorForBg($bgColor);
@endphp

<div class="space-y-4 min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                <flux:subheading size="xl" class="text-white text-base">
                    {{ $title }} 
                </flux:subheading>
            </div>
        </div>
    </div>

   

    <div class="px-4 space-y-3">

      <flux:select wire:model.live="selectedSyllabus" placeholder="Choisissez un thème...">
            @foreach($this->themes as $theme)
                <flux:select.option value="{{ $theme['attributes']['slug'] }}">{{ strtoupper($theme['attributes']['title']) }}</flux:select.option>
            @endforeach
        </flux:select>

        @php
            $items = [
              //  ['route' => route('questions', ['ue' => $ue, 'type' => 'question']), 'label' => '🎲 Question surprise'],
                ['route' => route('questions', ['ue' => $ue, 'type' => 'text']),     'label' => '🎥✍️ Traduis en français'],
                // ['route' => route('questions', ['ue' => $ue, 'type' => 'video-choice']), 'label' => '🎥👆 Choisis la bonne vidéo'],
                // ['route' => route('questions', ['ue' => $ue, 'type' => 'yes-no']),   'label' => '✅❌ Vrai / Faux'],
                ['route' => route('questions', ['ue' => $ue, 'type' => 'choice']),   'label' => '🎥📝 Choisir le bon mot'],
               // ['route' => route('questions', ['ue' => $ue, 'type' => 'match']),    'label' => '🔗 Associer les paires'],
                //['route' => route('questions', ['ue' => $ue, 'type' => 'tous']),     'label' => '🧠🔁 Révision complète'],
            ];
        @endphp

        @foreach($items as $item)
            <a wire:navigate href="{{ $item['route'] }}"
               class="flex items-center justify-between w-full px-5 py-4 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 active:scale-[0.98] transition cursor-pointer" style="background-color: {{$this->color}}">
                <span class="text-base font-semibold text-white">
                    {{ $item['label'] }}
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 72.4 72.4" class="w-6 h-6 shrink-0">
                    <circle fill="white" cx="36.2" cy="36.2" r="36.2"/>
                    <polygon fill="#2c333e" points="12.6 28.3 37.8 28.3 37.8 12.6 61.4 36.2 37.8 59.8 37.8 44.1 12.6 44.1 12.6 28.3"/>
                </svg>
            </a>
        @endforeach

    </div>
</div>