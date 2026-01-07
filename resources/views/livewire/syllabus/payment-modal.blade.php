{{-- resources/views/livewire/syllabus/payment-modal.blade.php --}}
<div class="fixed inset-0 flex items-center justify-center z-50 bg-black/80 transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-96 text-center transform transition-all duration-300">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Accès Syllabus</h2>

        <p class="text-gray-600 dark:text-gray-300 mb-6">
            Ce contenu nécessite l'achat du livre Syllabus accompagné d'un code d'accès.<br>
            Veuillez effectuer l'achat afin de débloquer le syllabus
            (<a href="https://www.facebook.com/share/v/1CHPFTKUvK/" target="_blank" class="text-blue-500 underline">voir le tutoriel</a>).
        </p>

        <a href="{{ $selectedLink }}" target="_blank" class="block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold text-lg w-full mb-4 transition-colors duration-200">
            Aller à la boutique
        </a>

        <button wire:click="closePaymentModal" class="w-full bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-6 py-2 rounded-lg transition-colors duration-200">
            Fermer
        </button>
    </div>
</div>