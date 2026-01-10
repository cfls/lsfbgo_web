@php
    $pairs = is_string($currentQuestion['options'])
        ? json_decode($currentQuestion['options'], true)
        : $currentQuestion['options'];
@endphp

@livewire('sign-video-match', [
    'pairs' => $pairs,
], key('sign-video-match-'.$currentIndex))