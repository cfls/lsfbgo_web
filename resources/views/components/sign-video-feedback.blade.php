@props([
    'isCorrect' => false,
    'message' => '',
    'image' => null,
    'currentQuestion' => [],
])

@if($isCorrect && $image)
    <div class="flex flex-col items-center justify-center animate-fade-in mt-2">
        {!! $image !!}
    </div>
@elseif(!$isCorrect && $image)
    <div class="flex justify-center mx-auto animate-fade-in ">
        <div class="mt-2 font-semibold text-white text-center flex flex-col items-center">
            {!! $image !!}
            @if(($currentQuestion['type'] ?? null) !== 'video-choice')
                Réponse correcte: {{ $message }}
            @endif
        </div>
    </div>
@endif

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.5s ease-out;
    }
</style>