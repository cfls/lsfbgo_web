<div class="flex justify-center mx-auto mb-5 font-medium text-sm   text-black dark:text-white rounded-lg text-center">
    Question {{ $currentIndex + 1 }} à {{ count($questions) }}
</div>

<h2 class="font-medium text-sm mb-4 text-center text-black dark:text-white">
    {{ $currentQuestion['question_text'] }}
</h2>