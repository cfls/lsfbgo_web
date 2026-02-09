@php
    use Illuminate\Support\Str;

    // Usar datos de la API si existen
    if ($syllabusData && isset($syllabusData['attributes'])) {
        $attributes = $syllabusData['attributes'];

        $bgClass = $attributes['hex_color'] ?? 'bg-gray-200';

    }
@endphp

<div  class="space-y-4 min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                @include('partials.quiz.svg.logo', ['class' => 'w-8 h-8'])
                <flux:subheading class="text-white text-base">
                    Choisissez le bon signe
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
               class="flex items-center justify-between w-full max-w-sm p-5  rounded-lg" style="background-color: {{$bgClass}}">
                <flux:label class="text-lg font-semibold text-white">
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
               href="{{ route('questions', ['ue' => $ue, 'type' => 'text']) }}"
               class="flex items-center justify-between w-full max-w-sm p-5  rounded-lg" style="background-color: {{$bgClass}}">
                <flux:label class="text-lg font-semibold text-white">
                    Traduire la LSFB
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
            <a wire:navigate
               href="{{ route('questions', ['ue' => $ue, 'type' => 'video-choice']) }}"
               class="flex items-center justify-between w-full max-w-sm p-5  rounded-lg" style="background-color: {{$bgClass}}">
                <flux:label class="text-lg font-semibold text-white">
                    Choix vidéo
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
            <a wire:navigate
               href="{{ route('questions', ['ue' => $ue, 'type' => 'yes-no']) }}"
               class="flex items-center justify-between w-full max-w-sm p-5  rounded-lg" style="background-color: {{$bgClass}}">
                <flux:label class="text-lg font-semibold text-white">
                    Oui / Non
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
            <a wire:navigate
               href="{{ route('questions', ['ue' => $ue, 'type' => 'choice']) }}"
               class="flex items-center justify-between w-full max-w-sm p-5  rounded-lg" style="background-color: {{$bgClass}}">
                <flux:label class="text-lg font-semibold text-white">
                    Choix
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
            <a wire:navigate
               href="{{ route('questions', ['ue' => $ue, 'type' => 'match']) }}"
               class="flex items-center justify-between w-full max-w-sm p-5  rounded-lg" style="background-color: {{$bgClass}}">
                <flux:label class="text-lg font-semibold text-white">
                    Associer les paires
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
            <a wire:navigate
               href="{{ route('questions', ['ue' => $ue, 'type' => 'tous']) }}"
               class="flex items-center justify-between w-full max-w-sm p-5  rounded-lg" style="background-color: {{$bgClass}}">
                <flux:label class="text-lg font-semibold text-white">
                    Révision complète du Syllabus {{strtoupper($ue) }}
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

        <a wire:navigate
           href=""
           class="hidden  items-center justify-between w-full max-w-sm p-5" style="background-color: {{$bgClass}}">
            <flux:label class="text-lg font-semibold text-white">
                Vidéo interactive
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


