<div x-cloak
     x-show="openSyllabusCompleted && !showFailModal"
     x-transition
     class="fixed inset-0 flex items-center justify-center bg-black/80 z-50 p-4">
    <div class="rounded-2xl shadow-xl p-4 sm:p-6 text-center w-full max-w-md mx-auto bg-white dark:bg-zinc-800 animate-fadeIn">

        <h1 class="text-xl sm:text-2xl font-bold text-white bg-gradient-to-br from-teal-500 to-purple-600 p-4 sm:p-5 mb-4 rounded-lg">
            🎉 Félicitations !
        </h1>

        <div class="my-4 flex justify-center">
            @include('partials.quiz.svg.logo')
        </div>

        <p class="mb-2 text-base sm:text-lg font-semibold text-gray-800 dark:text-white">
            Vous avez complété le Syllabus {{ $ueLabel }}
        </p>

        <p class="mb-6 text-base sm:text-lg text-gray-800 dark:text-white">
            Score: <span class="font-bold text-green-600" x-text="`${Math.round((liveScore / totalPoints) * 100)}%`"></span>
        </p>

        {{-- Botones compartir en redes --}}
        <div class="mb-6">
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-3">Partagez votre réussite</p>
            <div class="flex gap-3 justify-center">

                {{-- Facebook --}}
                <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.origin)}&quote=${encodeURIComponent('J\'ai complété le Syllabus ' + '{{ $ueLabel ?? '' }}' + ' sur LSFBGO ! 🎉')}`"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="Partager sur Facebook"
                   class="flex items-center justify-center w-12 h-12 rounded-full bg-[#1877F2] hover:bg-[#166fe0] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-6 h-6" aria-hidden="true">
                        <path d="M22 12.07C22 6.51 17.52 2 12 2S2 6.51 2 12.07c0 5.02 3.66 9.18 8.44 9.93v-7.03H7.9v-2.9h2.54V9.84c0-2.51 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.78-1.63 1.58v1.88h2.78l-.44 2.9h-2.34V22c4.78-.75 8.44-4.91 8.44-9.93Z"/>
                    </svg>
                </a>

                {{-- WhatsApp --}}
                <a :href="`https://wa.me/?text=${encodeURIComponent('J\'ai complété le Syllabus ' + '{{ $ueLabel ?? '' }}' + ' sur LSFBGO ! 🎉 ' + window.location.origin)}`"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="Partager sur WhatsApp"
                   class="flex items-center justify-center w-12 h-12 rounded-full bg-[#25D366] hover:bg-[#1ebe5b] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-6 h-6" aria-hidden="true">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.93.55 3.74 1.5 5.27L2 22l4.97-1.6a9.87 9.87 0 0 0 5.07 1.38c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2Zm0 18.06c-1.6 0-3.07-.43-4.34-1.18l-.31-.18-3.34 1.07 1.09-3.25-.2-.33a8.05 8.05 0 0 1-1.25-4.28c0-4.46 3.63-8.09 8.1-8.09s8.09 3.63 8.09 8.09-3.63 8.15-8.14 8.15Zm4.46-6.07c-.24-.12-1.43-.71-1.65-.79-.22-.08-.38-.12-.54.12-.16.24-.62.79-.76.95-.14.16-.28.18-.52.06-.24-.12-1-.37-1.9-1.17-.7-.62-1.18-1.39-1.32-1.62-.14-.24-.01-.37.11-.49.12-.12.27-.31.4-.47.13-.16.18-.27.27-.45.09-.18.04-.33-.04-.45-.08-.12-.59-1.42-.81-1.94-.21-.5-.43-.43-.6-.44h-.51c-.18 0-.46.07-.7.33-.24.27-.93.91-.93 2.21s.95 2.57 1.08 2.75c.13.18 1.79 2.73 4.34 3.72 2.55.99 2.55.66 3.01.62.46-.04 1.43-.58 1.63-1.15.2-.56.2-1.04.14-1.15-.06-.1-.24-.16-.48-.28Z"/>
                    </svg>
                </a>

            </div>
        </div>

        <div class="mt-5 flex flex-col sm:flex-row gap-3 justify-center">
            <flux:button
                    @click="window.location.href='{{ route('games', ['ue' => $slug]) }}'"
                    class="bg-gradient-to-br from-teal-500 to-purple-600 text-white w-full sm:w-auto">
                Continuer
            </flux:button>
        </div>

    </div>
</div>