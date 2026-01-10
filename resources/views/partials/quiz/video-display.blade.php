<div x-show="!slideOut"
     x-transition:enter="transition-all ease-out duration-500"
     x-transition:enter-start="opacity-0 translate-y-3"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition-all ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-3"
     wire:key="video-main-{{ $currentIndex }}">

    <x-sign-video-main
            :video="$currentQuestion['video']"
            :type="$currentQuestion['type']"
    />
</div>