<div x-cloak
     x-show="openCongrats && !showFailModal"
     x-transition
     class="fixed inset-0 flex items-center justify-center bg-black z-50 p-4">
    <div class="rounded-2xl shadow-xl p-4 sm:p-6 text-center w-full max-w-md mx-auto animate-fadeIn">
        <h1 class="text-xl sm:text-2xl font-bold text-white bg-sky-600 p-4 sm:p-5 mb-4 rounded-lg">
            Bravo !
        </h1>

        <div class="my-4 flex justify-center">
            @include('partials.quiz.svg.logo')
        </div>

        {{-- ✅ Usar Alpine.js con @entangle --}}
        <p class="mb-6 text-base sm:text-lg text-white">
            Score: <span class="font-bold text-green-600" x-text="`${liveScore} / ${totalPoints}`"></span>
        </p>

        <div class="mt-5 flex flex-col sm:flex-row gap-3 justify-center">
            <flux:button
                    wire:click="restartQuiz"
                    @click="openCongrats = false"
                    class="bg-blue-600 text-white hover:bg-blue-700 w-full sm:w-auto">
                Rejouer
            </flux:button>

            <flux:button
                    @click="window.location.href='{{ route('questions', ['ue' => $slug, 'type' => $type]) }}'"
                    class="bg-gray-500 text-white hover:bg-gray-600 w-full sm:w-auto">
                Suivant
            </flux:button>
        </div>
    </div>
</div>