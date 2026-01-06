<?php

namespace App\Livewire;

use Livewire\Component;

class Syllabus extends Component
{
    public function render()
    {
        return view('livewire.syllabus')->layout('components.layouts.app.home',[
            'title' => 'Syllabus',
        ]);
    }
}
