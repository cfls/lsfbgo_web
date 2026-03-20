
@props([
    'options' => [],
    'selectedAnswer' => null,
    'answered' => false,
])
<div class="flex flex-col justify-center gap-3 mb-4">

    @foreach($options as $option)
        <button
                class="px-5 py-2.5 rounded-lg border
                                   {{ $answered ? 'pointer-events-none opacity-70' : '' }}
                                   {{ $selectedAnswer === $option ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
                wire:click="selectAnswer(&quot;{{ $option }}&quot;)">
                 {{ str($option)->before(' / ')->before(' - ')->lower()->ucfirst() }}
        </button>
    @endforeach
</div>