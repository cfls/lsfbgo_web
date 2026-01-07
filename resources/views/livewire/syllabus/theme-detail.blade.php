{{-- resources/views/livewire/syllabus/theme-detail.blade.php --}}
<div class="space-y-4 min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-4">
            <div class="p-2 inline-block">
                {{-- Botón de regreso --}}
                <a wire:navigate href="{{ route('syllabus') }}" class="text-white mb-4 inline-flex items-center gap-2">
                    <flux:icon.arrow-left class="size-5"/>
                    Retour
                </a>

                <flux:subheading class="text-white text-xl pb-4">

                </flux:subheading>
            </div>

        </div>
    </div>

    @foreach($this->selectedTheme as $theme)
        @php
            $attrs = $theme['attributes'];
            $slugSyllabu = $attrs['slug_syllabu'];
            $slugTopic = $attrs['slug'];
            $title = $attrs['title'];
            $image = $attrs['image'];
        @endphp

        <a wire:navigate
           href=""
           class="flex flex-col items-center gap-2 hover:scale-105 transition-transform duration-300"
        >
            <img src="{{ $image }}"
                 alt="{{ $title }}"
                 class="w-1/2 h-auto object-cover rounded-xl shadow-md bg-blue-600"
            />
            <flux:text size="lg" class="text-center font-semibold text-gray-800 dark:text-white">
                {{ $title }}
            </flux:text>
        </a>
    @endforeach

</div>