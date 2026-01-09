@props([
    'isCorrect' => false,
    'message' => '',
    'image' => null,
    'currentQuestion' => [],
])

@if($isCorrect && $image)
    <div class="flex flex-col items-center justify-center">
        {!! $image !!}
    </div>
@elseif(!$isCorrect && $image)
    <div class="flex justify-center mx-auto">
        <div class="mt-2 font-semibold text-red-600 text-center flex flex-col items-center">
            {!! $image !!}
            @if(($currentQuestion['type'] ?? null) !== 'video-choice')
                Réponse correcte: {{ $message }}
            @endif
        </div>
    </div>
@endif
