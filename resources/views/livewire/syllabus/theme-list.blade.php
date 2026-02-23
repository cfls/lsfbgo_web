{{-- resources/views/livewire/syllabus/theme-list.blade.php --}}
<div class="space-y-6">
    <!-- Header with Gradient -->
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                    @include('partials.quiz.svg.logo', ['class' => 'w-8 h-8'])
                <flux:subheading class="text-white text-base">
                    {{$title}}
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="px-4 relative w-full">
        <div class="space-y-1">
            <p class="text-base text-black dark:text-white leading-relaxed">
                Sélectionnez les thèmes que vous souhaitez apprendre.
            </p>
        </div>

        {{-- Modal de pago --}}
{{--        @if ($showPaymentModal)--}}
{{--            @include('livewire.syllabus.payment-modal')--}}
{{--        @endif--}}

        {{-- Lista de syllabus --}}
        <div class="space-y-4 -mx-4">
            <div class="flex gap-3 overflow-x-auto pb-4 scrollbar-hide pl-4 pr-4 my-10 snap-x snap-mandatory scroll-smooth">


                @foreach ($results as $syllabu)


                    @php

                        $nameRoute = $this->optionGame ? 'games' : 'syllabus';
                        $isActive  = $syllabu['isActive'] ?? false;
                        $isStatus  = $syllabu['attributes']['status'];
                        $route     = route($nameRoute, ['ue' => $syllabu['attributes']['slug']]);
                        $image     = $syllabu['attributes']['image'];
                        $link      = $syllabu['attributes']['link'];
                    @endphp

{{--                    <flux:card class="bg-gradient-to-br hover:shadow-lg snap-center snap-always transition-shadow cursor-pointer size-40 rounded-lg {{ $isStatus === 0 ? 'hidden' : '' }}">--}}
                    <flux:card class="bg-gradient-to-br hover:shadow-lg snap-center snap-always transition-shadow cursor-pointer size-40 rounded-lg">
                        @if($this->optionGame == 0)
                            @if ($isActive || $this->userExcept == 16 || $this->userExcept == 23 || $this->userExcept == 48)
                                <a wire:navigate href="{{ $route }}" class="flex flex-col items-center justify-center cursor-pointer h-full">
                                    <div class="flex flex-col items-center justify-center text-center gap-1.5 p-2">
                                        <div class="size-32  flex items-center justify-center">
                                            <img src="{{ $image }}"  alt="syllabus image" class="rounded-full">
                                        </div>
                                    </div>
                                </a>
                            @else
                                <a class="flex flex-col items-center justify-center cursor-pointer h-full"  wire:click.prevent="openPaymentModal('{{ $link }}')"  role="button">
                                     <div class="flex flex-col items-center justify-center text-center gap-1.5 p-2">
                                        <div class="size-32  flex items-center justify-center">
                                            <img src="{{ $image }}"  alt="syllabus image" class="rounded-full">
                                        </div>
                                    </div>
                                 </a>
                            @endif
{{--                            @if ($isActive)--}}
{{--                                <a wire:navigate href="{{ $route }}" class="flex flex-col items-center justify-center cursor-pointer h-full">--}}
{{--                                    @else--}}
{{--                                 <a class="flex flex-col items-center justify-center cursor-pointer h-full"  wire:click.prevent="openPaymentModal('{{ $link }}')"  role="button">--}}
{{--                                     @endif--}}
{{--                                     <div class="flex flex-col items-center justify-center text-center gap-1.5 p-2">--}}
{{--                                        <div class="size-32  flex items-center justify-center">--}}
{{--                                            <img src="{{ $image }}"  alt="syllabus image" class="rounded-full">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                 </a>--}}
                            @else
                                <a wire:navigate href="{{ $route }}" class="flex flex-col items-center justify-center cursor-pointer h-full">
                                    <div class="flex flex-col items-center justify-center text-center gap-1.5 p-2">
                                        <div class="size-32  flex items-center justify-center">
                                            <img src="{{ $image }}"  alt="syllabus image" class="rounded-full">
                                        </div>
                                    </div>
                                </a>
                            @endif
                    </flux:card>
                @endforeach
            </div>
        </div>

    </div>
    <!-- Main Content Area with Horizontal Padding -->
    <div class="space-y-4 px-4">
        <!-- Camera Button Card -->
            <flux:card class="bg-gradient-to-br from-teal-500 to-purple-600   shadow-lg transition-all text-xl font-semibold border-2 border-amber-200 dark:border-amber-700 overflow-hidden">
                <flux:heading icon="camera" class="text-white dark:text-amber-100 mb-4">
                    Vous trouverez votre code unique à l'intérieur de la couverture arrière de votre syllabus. Si il ne s'y trouve pas, veuillez en faire la demande par mail à info@cfls.be en joignant la preuve de votre commande.
                </flux:heading>
                <div class="mb-6 text-center">
                    <flux:button wire:click="openInApp" icon="video-camera" class="py-4 px-6 bg-gradient-to-br from-blue-500 to-cyan-500 !text-white border-0 shadow-lg transition-all text-lg font-semibold [&>span]:!text-white">
                        Voir le tutoriel vidéo
                    </flux:button>
                </div>
            </flux:card>
    </div>
</div>