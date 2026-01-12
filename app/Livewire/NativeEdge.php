<?php

namespace App\Livewire;


use Livewire\Component;

class NativeEdge extends Component
{
    public string $title = 'Tableux de bord';


    public function render()
    {

        return view('livewire.native-edge');
    }

}