<div class="space-y-4 min-h-screen">
    @php
        use Illuminate\Support\Str;

        // Usar datos de la API si existen
        if ($syllabusData && isset($syllabusData['attributes'])) {
            $attributes = $syllabusData['attributes'];
            $ue = strtoupper(Str::replace('ue', 'UE ', $attributes['slug'] ?? $this->ue));
            $bgClass = $attributes['hex_color'] ?? 'bg-gray-200';
        }
    @endphp

    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                <a wire:navigate href="{{ route('games',['ue' => $this->ue]) }}" class="text-white inline-flex items-center gap-2">
                    <flux:icon.arrow-left-circle class="size-8"/>
                    @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                </a>
                <flux:subheading size="xl" class="text-white">
                    @php
                        switch($this->type):
                            case 'text':         $title = 'Traduis en français'; break;
                            // case 'video-choice': $title = 'Choisis la bonne vidéo';      break;
                            case 'choice':       $title = 'Choisir le bon mot';            break;
                            // case 'yes-no':       $title = 'Vrai / Faux';        break;
                            case 'match':        $title = 'Associer les paires'; break;
                            default:             $title = 'Question surprise'; break;
                        endswitch;
                    @endphp

                    {{$title}}
                </flux:subheading>
            </div>
        </div>
    </div>

    {{-- Lista de temas --}}
    <div class="bg-gray-300 p-5 w-full rounded-lg space-y-5 flex flex-col items-center justify-start
     max-h-[85vh] md:max-h-[65vh] overflow-y-auto no-scrollbar">

        @php
            // Normaliza lista de temas completados
            $doneThemes = collect($doneThemesData)
                ->map(fn($item) => [
                    'syllabus' => $item['syllabus'] ?? null,
                    'theme'    => $item['theme'] ?? null,
                    'type'     => $item['type'] ?? null,
                ]);

            // Obtén el syllabus actual de forma segura
            $currentSyllabus = $this->ue ?? ($doneThemes->first()['syllabus'] ?? null);

            // Cuenta cuántos temas completados del mismo tipo / syllabus
            $completedCount = $doneThemes
                ->filter(fn($item) => $item['type'] === ($type ?? null)
                                   && $item['syllabus'] === $currentSyllabus)
                ->count();


        @endphp

        @foreach($this->themes as $index => $theme)
            @php
                $attributes = $theme['attributes'];
                $themeSlug  = $attributes['slug'];



                // Verifica si ya fue completado
                $alreadyDone = $doneThemes->contains(fn($item) =>
                    $item['theme']    === $themeSlug &&
                    $item['type']     === $type &&
                    $item['syllabus'] === $currentSyllabus
                );

                // Desbloqueo progresivo
                $isUnlocked = $index === 0 || $index <= $completedCount;

                // Siempre desbloqueados
                $locked = false;
                $isUnlocked = true;

                // Colores: completado = verde, normal = color UE
                $colorClass = $alreadyDone ? '#34eb40' : $bgClass;

                // Siempre se puede acceder
                $link = route('syllabus.play', [
                    'ue'    => $this->ue,
                    'type'  => $type,
                    'theme' => $themeSlug,
                ]);
            @endphp

            <a href="{{ $link }}" class="w-full max-w-sm">
                <div class="flex items-center justify-between p-5 border border-gray-200 rounded-lg shadow-sm text-white transition duration-300" style="background-color: {{ $colorClass }}">
                    <div class="flex items-center gap-2">
                        <flux:label class="text-lg font-semibold text-white">
                            {{ $loop->iteration }}. {{ ucfirst($attributes['title']) }}
                        </flux:label>
                    </div>

                    {{-- Iconos según estado --}}
                    @if($locked)
                        {{-- 🔒 Bloqueado --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    @elseif($alreadyDone)
                        {{-- ⭐ Completado --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    @else
                        {{-- ▶️ Desbloqueado --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>