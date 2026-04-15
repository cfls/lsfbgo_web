@props([
    'options' => [],
    'selectedAnswer' => null,
    'answered' => false,
])

<div class="flex flex-col items-center gap-3 mb-4">

    @foreach($options as $option)
        <button
                class="w-full max-w-sm md:max-w-md px-5 py-2.5 font-bold rounded-lg border-2 border-black shadow-sm hover:bg-gray-50 transition
                {{ $answered ? 'pointer-events-none opacity-70' : '' }}
                {{ $selectedAnswer === $option ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-black' }}"
                wire:click="selectAnswer(&quot;{{ $option }}&quot;)"
        >
            {{ str($option)->before(' / ')->before(' - ')->lower()->ucfirst() }}
        </button>
    @endforeach

</div>