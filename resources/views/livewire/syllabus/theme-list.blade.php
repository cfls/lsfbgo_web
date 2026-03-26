{{-- resources/views/livewire/syllabus/theme-list.blade.php --}}
<div class="space-y-6">
       
      
         @if ($showPaymentModal)
            @include('partials.quiz.modals.code', ['link' => $selectedLink, 'theme' => $theme])
        @endif
    <!-- Header -->
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                <flux:subheading size="xl" class="text-white text-base">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="px-4 space-y-6">

        <p class="text-sm font-bold text-gray-600 dark:text-gray-300">
            Sélectionnez les thèmes que vous souhaitez apprendre.
        </p>

        {{-- Grid 2 columnas --}}
        <div class="grid grid-cols-2 gap-4">
            @foreach ($results as $syllabu)
                @php
                
                    $nameRoute = $this->optionGame ? 'games' : 'syllabus';
                    $isActive  = $syllabu['isActive'] ?? false;
                    $isStatus  = $syllabu['attributes']['status'];
                    $route     = route($nameRoute, ['ue' => $syllabu['attributes']['slug']]);
                    $image     = $syllabu['attributes']['image'];
                    $link      = $syllabu['attributes']['link'];
                  
                   
                @endphp

                <flux:card class="hover:shadow-lg transition-shadow cursor-pointer rounded-xl p-3">
                    @if($this->optionGame == 0)                       
                      
                        @if ($isActive || $this->role == 'admin')
                            <a wire:navigate href="{{ $route }}" class="flex items-center justify-center h-full">
                                <img src="{{ $image }}" alt="syllabus image" class="w-32 h-32 rounded-full object-cover">
                            </a>
                        @else
                            <a wire:click.prevent="openPaymentModal('{{ $link }}', '{{ $syllabu['attributes']['slug'] }}')" role="button" class="flex items-center justify-center h-full">
                                <img src="{{ $image }}" alt="syllabus image" class="w-32 h-32 rounded-full object-cover">
                            </a>
                        @endif
                    @else
                        <a wire:navigate href="{{ $route }}" class="flex items-center justify-center h-full">
                            <img src="{{ $image }}" alt="syllabus image" class="w-32 h-32 rounded-full object-cover">
                        </a>
                    @endif
                </flux:card>
            @endforeach
        </div>

        {{-- Card tutoriel compact --}}
        <flux:card class="bg-gradient-to-br from-teal-500 to-purple-600 border border-amber-200 dark:border-amber-700">
            <p class="text-white text-xs leading-relaxed mb-3">
                Votre code unique se trouve à l'intérieur de la couverture arrière de votre syllabus. Pour toute demande, écrivez à info@cfls.be.
            </p>
            <flux:button
                wire:click="openInApp"
                icon="video-camera"
                class="w-full bg-gradient-to-br from-blue-500 to-cyan-500 !text-white border-0 shadow-sm text-sm font-semibold [&>span]:!text-white"
            >
                Voir le tutoriel vidéo
            </flux:button>
        </flux:card>

    </div>
</div>