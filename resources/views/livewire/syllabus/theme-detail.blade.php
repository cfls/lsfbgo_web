{{-- resources/views/livewire/syllabus/theme-detail.blade.php --}}
<div class="space-y-4 min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-4">
            <div class="p-2 inline-block">
                {{-- Botón de regreso --}}
                <a wire:navigate href="{{ route('syllabus') }}" class="text-white mb-4 inline-flex items-center gap-2">
                    <flux:icon.arrow-left-circle class="size-5"/>

                </a>

                <flux:subheading class="text-white text-xl pb-4 tex-center">
                    {{ 'UE ' . preg_replace('/ue(\d+)-.+/', '$1', $this->selectedTheme[0]['attributes']['slug_syllabu']) }}
                </flux:subheading>
            </div>

        </div>
    </div>

    @foreach($this->selectedTheme as $theme)
        @php
            $attrs = $theme['attributes'];
            $ue = $attrs['slug_syllabu'];
            $theme = $attrs['slug'];
            $title = $attrs['title'];
            $image = $attrs['image'];
        @endphp

        <a wire:navigate
           href="{{ route('syllabus.themes', ['ue' => $ue, 'theme' => $theme]) }}"
           class="flex flex-col items-center gap-2 hover:scale-105 transition-transform duration-300"
        >
            <div class="size-32 relative bg-gray-300 rounded-xl animate-pulse overflow-hidden">
                <img src="{{ $image }}"
                     alt="{{ $title }}"
                     class="size-32 object-cover rounded-xl shadow-md opacity-0 transition-opacity duration-500"
                     onload="this.style.opacity='1'; this.parentElement.classList.remove('animate-pulse', 'bg-gray-300')"
                />
            </div>
            <flux:text size="lg" class="text-center font-semibold text-gray-800 dark:text-white">
                {{ $title }}
            </flux:text>
        </a>
    @endforeach

</div>