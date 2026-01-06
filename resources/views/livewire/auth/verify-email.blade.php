<div class="mt-4 flex flex-col gap-6">
    <flux:text class="text-center">
        Entrez le code de vérification envoyé à votre adresse e-mail pour activer votre compte.
    </flux:text>

    @if ($message)
        <flux:text class="text-center font-medium !text-green-600">
            {{ $message }}
        </flux:text>
    @endif

    @if ($error)
        <flux:text class="text-center font-medium !text-red-600">
            {{ $error }}
        </flux:text>
    @endif

    <form wire:submit.prevent="verify" class="flex flex-col gap-4 items-center">
        <input type="text" wire:model="code" maxlength="6"
               class="w-32 text-center border rounded-lg p-2 tracking-widest"
               placeholder="entrer le code">
        <flux:button type="submit" variant="primary" class="w-full">
            Vérifier le code
        </flux:button>
    </form>

    <div class="flex flex-col items-center justify-between space-y-3 mt-4">
        <flux:button wire:click="resendCode" variant="primary" class="w-full">
            Renvoyer le code
        </flux:button>


    </div>
</div>
