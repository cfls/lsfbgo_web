<?php

namespace App\Livewire;

use Livewire\Component;

class Numbers extends Component
{
    public string $title = 'Les chiffres de 0 à 1000';
    public function render()
    {
        return view('livewire.numbers')->layout('components.layouts.app.home', [
            'title' => 'Les chiffres de 0 à 1000',
        ]);
    }
}
