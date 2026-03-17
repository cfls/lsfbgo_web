<?php

namespace App\Livewire;


use Livewire\Component;

class NativeEdge extends Component
{
    public string $title = 'Tableau de bord';


    public function render()
    {

        return view('livewire.native-edge');
    }

}