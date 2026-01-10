<div x-show="openCongrats && !showFailModal"
     x-transition
     class="fixed inset-0 flex items-center justify-center bg-black/60 z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 text-center w-3/6 max-w-5/6 mx-auto animate-fadeIn">
        <h1 class="text-2xl font-bold text-white bg-sky-600 p-5 mb-4 rounded-lg">
            Bravo !
        </h1>

        @include('partials.quiz.svg.success')

        <p class="text-gray-700 mb-4">
            Score: <span class="font-bold text-green-600">{{ $score }} / {{ count($questions) * 10 }}</span>
        </p>

        <div class="mt-5 flex gap-3 justify-center">
            <flux:button
                    wire:click="restartQuiz"
                    @click="openCongrats = false"
                    class="bg-blue-600 text-white hover:bg-blue-700">
                Rejouer
            </flux:button>

            <flux:button
                    wire:click="$redirect('/syllabus')"
                    class="bg-gray-500 text-white hover:bg-gray-600">
                Retour
            </flux:button>
        </div>
    </div>
</div>