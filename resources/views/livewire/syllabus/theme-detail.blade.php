{{-- resources/views/livewire/syllabus/theme-detail.blade.php --}}
<div class="space-y-4 min-h-screen">
    

    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2 flex items-center gap-3">
        <a wire:navigate href="{{ route('syllabus') }}" class="text-white shrink-0">
                <flux:icon.arrow-left-circle class="size-10"/>
            </a>
            @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20 shrink-0'])
            <flux:subheading size="xl" class="text-white text-base">
                {{ $title }}
            </flux:subheading>
        </div>
    </div>
        
     

    <div class="px-4 grid grid-cols-2 gap-4">
        @foreach($this->selectedTheme as $theme)
            @php
                $attrs  = $theme['attributes'];
                $ue     = $attrs['slug_syllabu'];
                $slug   = $attrs['slug'];
                $title  = $attrs['title'];
                $image  = $attrs['image'];
            @endphp

            <a wire:navigate
               href="{{ route('syllabus.themes', ['ue' => $ue, 'theme' => $slug]) }}"
               class="flex flex-col items-center gap-2 active:scale-95 transition-transform duration-200"
            >
                <flux:card class="w-full flex flex-col items-center gap-2 p-3">
                    <div class="w-full aspect-square relative bg-gray-200 dark:bg-zinc-700 rounded-xl animate-pulse overflow-hidden">
                        <img src="{{ $image }}"
                             alt="{{ $title }}"
                             class="w-full h-full object-cover rounded-xl opacity-0 transition-opacity duration-500"
                             onload="this.style.opacity='1'; this.parentElement.classList.remove('animate-pulse', 'bg-gray-200')"
                        />
                    </div>
                    <span class="text-sm font-semibold text-center text-gray-800 dark:text-white leading-tight">
                        {{ $title }}
                    </span>
                </flux:card>
            </a>
        @endforeach
    </div>

</div>