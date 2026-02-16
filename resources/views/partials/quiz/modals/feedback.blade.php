{{-- Modal de Feedback --}}
<div x-show="openFeedback"
     x-cloak
     @keydown.escape.window="openFeedback = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    {{-- Overlay --}}
    <div x-show="openFeedback"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         @click="openFeedback = false">
    </div>

    {{-- Modal Panel --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="openFeedback"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-zinc-900 text-left shadow-xl transition-all w-full max-w-lg"
             @click.stop>

            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <h3 class="text-xl font-bold text-white">
                            Envoyer un commentaire
                        </h3>
                    </div>
                    <button @click="openFeedback = false"
                            class="text-white hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6">
                {{-- Tipo de feedback --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Type de commentaire
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <button @click="feedbackType = 'bug'"
                                :class="feedbackType === 'bug'
                                    ? 'bg-red-100 border-red-500 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                    : 'bg-gray-100 border-gray-300 text-gray-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300'"
                                class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 transition hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-xs font-medium">Bug</span>
                        </button>

                        <button @click="feedbackType = 'suggestion'"
                                :class="feedbackType === 'suggestion'
                                    ? 'bg-blue-100 border-blue-500 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
                                    : 'bg-gray-100 border-gray-300 text-gray-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300'"
                                class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 transition hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <span class="text-xs font-medium">Suggestion</span>
                        </button>

                        <button @click="feedbackType = 'question'"
                                :class="feedbackType === 'question'
                                    ? 'bg-green-100 border-green-500 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                    : 'bg-gray-100 border-gray-300 text-gray-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-300'"
                                class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 transition hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-medium">Question</span>
                        </button>
                    </div>
                </div>

                {{-- Mensaje --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Votre message
                    </label>
                    <textarea x-model="feedbackMessage"
                              rows="4"
                              placeholder="Décrivez votre problème, suggestion ou question..."
                              class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-zinc-800 dark:text-white resize-none"
                    ></textarea>
                </div>

                {{-- Info de contexto --}}
                <div class="hidden bg-gray-50 dark:bg-zinc-800 rounded-lg p-3 mb-4">
                    <div class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium">Contexte inclus :</p>
                            <p class="text-xs">Question {{ $currentIndex + 1 }} / {{ count($questions) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 dark:bg-zinc-800 px-6 py-4 flex gap-3">
                <button @click="openFeedback = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-zinc-700 dark:text-gray-200 dark:border-zinc-600 dark:hover:bg-zinc-600 transition">
                    Annuler
                </button>
                <button @click="submitFeedback()"
                        :disabled="feedbackSending || !feedbackMessage.trim()"
                        :class="feedbackSending || !feedbackMessage.trim()
                            ? 'opacity-50 cursor-not-allowed'
                            : 'hover:from-blue-600 hover:to-purple-700'"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg transition">
                    <span x-show="!feedbackSending">Envoyer</span>
                    <span x-show="feedbackSending" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Envoi...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
