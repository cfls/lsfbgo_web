<div class="space-y-4 min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                <a wire:navigate href="{{ route('games', ['ue' => $this->ue]) }}" class="text-white inline-flex items-center gap-2">
                    <flux:icon.arrow-left-circle class="size-8"/>
                    @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                </a>
                <flux:subheading size="xl" class="text-white">{{ $title }}</flux:subheading>
            </div>
        </div>
    </div>

    {{-- Lista de temas --}}
    <div class="bg-gray-300 p-5 w-full rounded-lg space-y-5 flex flex-col items-center justify-start
                max-h-[85vh] md:max-h-[65vh] overflow-y-auto no-scrollbar">
        @forelse($cards as $card)
            <a href="{{ $card['link'] }}" class="w-full max-w-sm">
                <div class="flex items-center justify-between p-5 border border-gray-200 rounded-lg shadow-sm transition duration-300"
                     style="background-color: {{ $card['colorHex'] }}; color: {{ $card['textColor'] }};">
                    <flux:label class="text-lg font-semibold" style="color: {{ $card['textColor'] }};">
                        {{ $card['iteration'] }}. {{ $card['title'] }}
                    </flux:label>

                    @if($card['done'])
                        {{-- ⭐ Completado --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="color: {{ $card['textColor'] }};">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    @else
                        {{-- ▶️ Disponible --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="color: {{ $card['textColor'] }};">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @endif
                </div>
            </a>
        @empty
            <p class="text-gray-600 py-8">No hay temas disponibles.</p>
        @endforelse
    </div>
</div>