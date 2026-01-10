<div x-show="showFailModal && !openCongrats"
     x-transition.opacity.duration.300ms
     class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 text-center w-3/6 max-w-5/6 mx-auto animate-fadeIn">
        <h1 class="text-2xl font-bold text-white bg-red-600 p-5 mb-4 rounded-lg">
            Dommage !
        </h1>

        @include('partials.quiz.svg.failure')

        <p class="text-gray-700 mb-2">
            Score: <span class="font-bold text-red-600">{{ $score }} / {{ count($questions) * 10 }}</span>
        </p>

        <p class="text-gray-600 mb-4">
            Pourcentage: <span x-text="score + '%'"></span>
        </p>

        <p class="text-sm text-gray-500 mb-4">
            Vous devez obtenir au moins 80% pour réussir.
        </p>

        <button
                wire:click="restartQuiz"
                @click="showFailModal = false"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition mt-5">
            Recommencer le quiz
        </button>
    </div>
</div>