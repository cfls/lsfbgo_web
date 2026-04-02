
@props([
    'options' => [],
    'selectedAnswer' => null,
    'answered' => false,
])
<div class="flex flex-col justify-center gap-3 mb-4">

    @foreach($options as $option)
      <button
                class="px-5 py-2.5 font-bold rounded-lg border-2 border-black shadow-sm hover:bg-gray-50 transition w-full
                    {{ $answered ? 'pointer-events-none opacity-70' : '' }}
                    {{ $selectedAnswer === $option ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-black' }}"
                wire:click="selectAnswer(&quot;{{ $option }}&quot;)"
            >
                {{ str($option)->before(' / ')->before(' - ')->lower()->ucfirst() }}
    </button>
    @endforeach
</div>