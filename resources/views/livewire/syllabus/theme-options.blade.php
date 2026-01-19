@php
    use Illuminate\Support\Str;

    // prend uniquement la première partie avant le "-"
    $ue = Str::before($this->ue, '-');




    // couleurs par unité
    $colors = [
        'ue1' => '#027374',
        'ue2' => '#f46070',
        'ue3' => '#f3c543',
    ];

    // couleur par défaut si non définie
    $bgColor = $colors[$ue] ?? '#e5e7eb';
@endphp



<div  class="space-y-4 min-h-screen">  
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                @include('partials.quiz.svg.logo', ['class' => 'w-8 h-8'])
                <flux:subheading class="text-white text-base">
                    Jeu interactif
                </flux:subheading>
            </div>
        </div>
    </div>
    <div class="bg-gray-300 p-5 w-full  rounded-lg space-y-5 flex flex-col items-center justify-center">


        @foreach($this->results as $section)
            @php
                $attributes = $section['attributes'];
            @endphp

            <a wire:navigate
               href="{{ route('questions', ['ue' => $ue, 'type' => $attributes['type']]) }}"
               class="flex items-center justify-between w-full max-w-sm p-5 bg-orange-500 rounded-lg hover:bg-red-700">
                <flux:label class="text-lg font-semibold text-white dark:text-gray-900">
                    {{ ucfirst($attributes['name']) }}
                </flux:label>
                <svg id="Calque_1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 72.4 72.4" class="w-5 h-5">
                    <!-- Generator: Adobe Illustrator 29.1.0, SVG Export Plug-In . SVG Version: 2.1.0 Build 142)  -->
                    <defs>
                        <style>
                            .st0 {
                                fill: #2c333e;
                            }

                            .st1 {
                                fill: #fff;
                            }
                        </style>
                    </defs>
                    <circle class="st1" cx="36.2" cy="36.2" r="36.2"/>
                    <polygon class="st0" points="12.6 28.3 37.8 28.3 37.8 12.6 61.4 36.2 37.8 59.8 37.8 44.1 12.6 44.1 12.6 28.3"/>
                </svg>
            </a>
        @endforeach

        <a wire:navigate
           href=""
           class="hidden  items-center justify-between w-full max-w-sm p-5 bg-orange-500 rounded-lg hover:bg-red-700">
            <flux:label class="text-lg font-semibold text-gray-900 dark:text-white">
                Vidéo interactif
            </flux:label>
            <svg id="Calque_1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  class="w-5 h-5">
                <defs>
                    <style>
                        .cls-1{fill:var(--cfls-1);}
                        .cls-2{fill:var(--cfls-2);
                            stroke:var(--cfls-stroke);
                            stroke-miterlimit:10;
                        }
                    </style>
                </defs>
                <circle class="cls-2" cx="12" cy="12" r="11.5"/>
                <polygon class="cls-1" points="4.51 9.51 12.5 9.5 12.5 4.51 19.99 12 12.5 19.5 12.5 14.5 4.51 14.5 4.51 9.51"/>
            </svg>
        </a>
    </div>
</div>


