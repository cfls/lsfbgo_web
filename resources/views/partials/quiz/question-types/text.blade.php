<div class="flex justify-center mt-4">
    <input type="text"
           wire:model="userInput"
           inputmode="text"
           enterkeyhint="done"
           autocomplete="off"
           autocorrect="off"
           autocapitalize="none"
           spellcheck="false"
           class="border rounded px-3 py-2 text-center bg-white w-1/2 text-black"
           placeholder="Votre réponse"
           @keydown.enter.prevent="$el.blur();$wire.set('userInput', $event.target.value).then(() => $wire.checkAnswer())" />
</div>