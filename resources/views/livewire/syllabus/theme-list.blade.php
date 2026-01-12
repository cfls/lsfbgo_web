{{-- resources/views/livewire/syllabus/theme-list.blade.php --}}
<div class="space-y-4 bg-white min-h-screen">
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        {{-- Tu header actual --}}
        <div class="px-4">
            <div class="p-2 inline-block">
                {{-- Tu SVG --}}
                <flux:subheading class="text-white text-xl pb-4">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="px-4 relative w-full">
        <div class="space-y-1">
            <div class="absolute top-0 right-0 -mt-5">
                <flux:icon.light-bulb class="size-40 text-yellow-400/10"/>
            </div>
            <p class="text-base text-black leading-relaxed">
                Sélectionnez les thèmes que vous souhaitez apprendre.
            </p>
        </div>

        {{-- Modal de pago --}}
        @if ($showPaymentModal)
            @include('livewire.syllabus.payment-modal')
        @endif

        {{-- Lista de syllabus --}}
        <div class="space-y-4 -mx-4">
            <div class="flex gap-3 overflow-x-auto pb-4 scrollbar-hide pl-4 pr-4 my-10 snap-x snap-mandatory scroll-smooth">

                @foreach ($results as $syllabu)

                    @php
                        $nameRoute = $this->optionGame ? 'games' : 'syllabus';
                        $userMatch = $verifyUser->firstWhere('attributes.theme', $syllabu['attributes']['slug']);
                        $isActive  = $userMatch['attributes']['active'] ?? null;
                        $route     = route($nameRoute , ['ue' => $syllabu['attributes']['slug']]);
                        $image     = $syllabu['attributes']['image'];
                        $link      = $syllabu['attributes']['link'];
                    @endphp

                    <flux:card class="bg-gradient-to-br hover:shadow-lg snap-center snap-always transition-shadow cursor-pointer size-40 rounded-lg">
                        <a wire:navigate href="{{ $route }}"
                                class="flex flex-col items-center justify-center cursor-pointer h-full"
                        >
                            <div class="flex flex-col items-center justify-center text-center gap-1.5 p-2">
                                <div class="size-32 bg-white/30 flex items-center justify-center">
                                    <img
                                            src="{{ $image }}"
                                            alt="syllabus image"
                                            class="rounded-full"
                                    >
                                </div>
                            </div>
                        </a>
                    </flux:card>
                @endforeach
            </div>
        </div>
    </div>
</div>