@props(['video', 'type', 'currentIndex'])

<div
     x-transition:enter="transition-all ease-out duration-500"
     wire:key="video-main-{{ $currentIndex }}">
    <x-sign-video-main :video="$video" :type="$type"/>
</div>