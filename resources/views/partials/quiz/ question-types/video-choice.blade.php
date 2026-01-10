<div x-show="!slideOut"
     x-transition:enter="transition-all ease-out duration-500"
     x-transition:enter-start="opacity-0 translate-y-3"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition-all ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-3"
     wire:key="video-options-{{ $currentIndex }}">

    <x-sign-video-options
            :options="$currentQuestion['options']"
            :correct="$currentQuestion['answer']"
            :selected="$selectedAnswer"
            :answered="$answered"
    />
</div>