<div class="space-y-4 min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-4">
            <div class="p-2 inline-block">
                @include('partials.quiz.svg.logo')
                <flux:subheading class="text-white text-xl pb-4">
                    Maîtrisez la LSFB grâce à LSFBGo — une application pensée pour l'accessibilité et l'inclusion.
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="px-4 py-4">
        <div class="grid grid-cols-2 gap-4">
            @foreach($topics as $topic)
                <a href="{{ route($topic['route']) }}" wire:key="demo-{{ $loop->index }}">
                    <flux:card class="bg-gradient-to-br {{ $topic['gradient'] }} hover:shadow-lg transition-shadow cursor-pointer rounded-xl w-full">
                        <div class="flex flex-col items-center justify-center text-center gap-2 py-6 px-3">
                            <div class="size-16 rounded-full bg-white/30 flex items-center justify-center">
                                <flux:icon icon="{{ $topic['icon'] }}" class="size-10 text-white"/>
                            </div>
                            <h1 class="text-sm font-bold text-white leading-tight">{{ $topic['title'] }}</h1>
                        </div>
                    </flux:card>
                </a>
            @endforeach
        </div>
    </div>

    <div class="pb-32"></div>
</div>