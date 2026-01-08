<div class="space-y-4 min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-4">
            <div class="p-2 inline-block">
                {{-- Botón de regreso --}}
                <a wire:navigate href="{{ route('syllabus') }}" class="text-white mb-4 inline-flex items-center gap-2">
                    <flux:icon.arrow-left-circle class="size-5"/>

                </a>

                <flux:subheading class="text-white text-xl pb-4">

                </flux:subheading>
            </div>

        </div>
    </div>
    <div class="flex-1 overflow-y-auto px-3 py-6 scroll-touch no-scrollbar">
       

        <flux:heading>
            <flux:text class="text-center uppercase text-2xl" variant="strong">
                {{ $this->results['attributes']['title'] }}
            </flux:text>
        </flux:heading>

        <!-- 🔍 Campo de búsqueda -->
        <div x-data="{ search: '' }" class="mt-5">
            <flux:input
                    icon="magnifying-glass"
                    placeholder="Rechercher un signe..."
                    x-model="search"
                    class="w-full mb-4"
            />

            <!-- Lista con scroll -->
            <div class="space-y-2 max-h-[60vh] overflow-y-auto pr-2 scroll-smooth no-scrollbar">
                @foreach($this->results['attributes']['videos'] as $video)
                    <template x-if="'{{ strtolower($video['title']) }}'.includes(search.toLowerCase())">
                        <div class="space-y-2 bg-orange-400 px-4 py-5 rounded-lg  font-bold hover:bg-orange-700 transition">
                            <a href="{{ route('syllabus.theme', [
                                $this->results['attributes']['slug_syllabu'],
                                $this->results['attributes']['slug'],
                                'id' => $video['id']
                            ]) }}">

                                <flux:text class="flex font-bold items-center justify-between text-black">
                                    <span>{{ $video['title'] }}</span>
                                    <flux:icon.arrow-right-circle class="size-6 text-black" />
                                </flux:text>
                            </a>
                        </div>
                    </template>
                @endforeach
            </div>
        </div>
    </div>

</div>
