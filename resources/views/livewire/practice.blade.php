<div class="space-y-4 min-h-screen bg-gray-50">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)]">
        <div class="px-4 py-4">
            <div class="flex flex-col items-start gap-3">
                @include('partials.quiz.svg.logo')

                <flux:subheading class="text-white text-base sm:text-lg leading-snug max-w-md">
                    Maîtrisez la LSFB grâce à LSFBGo — une application pensée pour l'accessibilité et l'inclusion.
                </flux:subheading>
            </div>
        </div>
    </div>

    {{-- Grid --}}
    <div class="px-4 pb-6">
        <div class="grid grid-cols-2 gap-4">
            @foreach($topics as $topic)
                <a href="{{ route($topic['route']) }}" wire:key="demo-{{ $loop->index }}" class="block h-full">

                    <flux:card class="bg-gradient-to-br {{ $topic['gradient'] }} rounded-2xl shadow-md active:scale-95 transition-transform duration-150 h-full">

                        <div class="flex flex-col justify-between h-full p-4">

                            {{-- Icon --}}
                            <div class="flex justify-center">
                                <div class="size-14 rounded-full bg-white/25 backdrop-blur flex items-center justify-center">
                                    <flux:icon icon="{{ $topic['icon'] }}" class="size-8 text-white"/>
                                </div>
                            </div>

                            {{-- Title --}}
                            <h2 class="text-sm font-semibold text-white text-center leading-snug line-clamp-2 min-h-[2.75rem] flex items-center justify-center">
                                {{ $topic['title'] }}
                            </h2>

                        </div>

                    </flux:card>
                </a>
            @endforeach
        </div>
    </div>

    <div class="pb-28"></div>
</div>