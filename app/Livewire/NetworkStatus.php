<?php

namespace App\Livewire;

use Livewire\Component;
use Native\Mobile\Facades\Network;

class NetworkStatus extends Component
{
    public $connected = false;
    public $type = null;

    protected $listeners = ['refreshNetwork' => 'checkNetwork'];

    public function mount()
    {
        $this->checkNetwork();
    }

    public function checkNetwork()
    {
        try {
            $status = Network::status();
        } catch (\Throwable $e) {
            $status = null;
        }

        if (!$status || !$status->connected) {
            $this->connected = false;
            $this->type = null;
            return;
        }

        $this->connected = true;
        $this->type = $status->type ?? null;
    }

    public function render()
    {
        return view('livewire.network-status');
    }
}
