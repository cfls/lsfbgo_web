<div  class="space-y-4   min-h-screen">
    <div
            class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none ">
        <div class="px-4">
            <div class="p-2 inline-block">

                @include('partials.quiz.svg.logo')

                <flux:subheading class="text-white text-xl pb-4">
                    Maîtrisez la LSFB grâce à LSFBGo — une application pensée pour l’accessibilité et l’inclusion.
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="px-4 relative w-full">
        <div class="space-y-4 -mx-4">
            <div class="flex gap-3 overflow-x-auto pb-4 scrollbar-hide pl-4 pr-4 my-10 snap-x snap-mandatory scroll-smooth">
                @foreach($topics as $topic)
                    <a href="{{route($topic['route'])}}" wire:key="demo-{{ $loop->index }}" class=" shrink-0">
                        <flux:card
                                class="bg-gradient-to-br {{ $topic['gradient'] }} hover:shadow-lg snap-center snap-always transition-shadow cursor-pointer size-40 rounded-lg">
                            <div class="flex flex-col items-center justify-center text-center gap-1.5 h-full p-2">
                                <div
                                        class="size-16 rounded-full bg-white/30 flex items-center justify-center">
                                    <flux:icon icon="{{$topic['icon']}}" class="size-12 text-white"/>
                                </div>
                                <div>
                                    <h1 class="text-sm font-bold text-white leading-tight">{{ $topic['title'] }}</h1>
                                </div>
                            </div>
                        </flux:card>
                    </a>
                @endforeach
            </div>

        </div>

    </div>
    <div class="pb-32"></div>
</div>

