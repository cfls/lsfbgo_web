<?php

namespace App\Livewire;

use Livewire\Component;

class AlphabetSigner extends Component
{
    public string $title = 'Alphabet LSFB';
    public function render()
    {
        return view('livewire.alphabet-signer')->layout('components.layouts.app.home', [
            'title' => $this->title,
        ]);
    }
}
