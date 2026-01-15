<div x-show="showFailModal && !openCongrats"
     x-transition.opacity.duration.300ms
     class="fixed inset-0 flex items-center justify-center bg-black z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6 text-center w-full max-w-md mx-auto animate-fadeIn">
        <h1 class="text-xl sm:text-2xl font-bold text-white bg-red-600 p-4 sm:p-5 mb-4 rounded-lg">
            Dommage !
        </h1>

        <div class="my-4 flex justify-center">
            @include('partials.quiz.svg.logo')
        </div>

        <p class="text-gray-700 mb-2 text-base sm:text-lg">
            Score: <span class="font-bold text-red-600">{{ $score }} / {{ count($questions) * 10 }}</span>
        </p>

        <p class="text-white mb-4 text-sm sm:text-base">
            Pourcentage: <span x-text="score + '%'"></span>
        </p>

        <p class="text-xs sm:text-sm text-white mb-6">
            Vous devez obtenir au moins 80% pour réussir.
        </p>

        <button
                wire:click="restartQuiz"
                @click="showFailModal = false"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition w-full sm:w-auto">
            Recommencer le quiz
        </button>
    </div>
</div>