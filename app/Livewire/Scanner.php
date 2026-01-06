<?php

namespace App\Livewire;

use Livewire\Component;

class Scanner extends Component
{
    public function render()
    {
        return view('livewire.scanner')->layout('components.layouts.app.home', [
            'title' => 'Scanner',
        ]);
    }
}
