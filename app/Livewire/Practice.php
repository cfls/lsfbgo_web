<?php

namespace App\Livewire;

use Livewire\Component;

class Practice extends Component
{
    public function render()
    {
        return view('livewire.practice')->layout('components.layouts.app.home', [
            'title' => 'Exercices LSFB',
        ]);
    }
}
