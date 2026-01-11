<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class NativeEdge extends Component
{
    public string $title = 'Tableux de bord';
    public string $token = '';

    public function mount()
    {
        $this->token = Session::get('data.token', '');
    }

    public function render()
    {
        return view('livewire.native-edge');
    }

}