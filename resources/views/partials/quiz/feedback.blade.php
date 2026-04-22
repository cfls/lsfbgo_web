<x-sign-video-feedback
        :isCorrect="$isCorrect"
        :message="$message"
        :image="$image"
        :userAnswer="$userInput ?: $selectedAnswer"
        :currentQuestion="$currentQuestion"
/>