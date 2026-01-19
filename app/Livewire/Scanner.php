<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Scanner\CodeScanned;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\Scanner as ScannerFacade;

class Scanner extends Component
{

    public $data;

    public $format;

    public $requestedFormat = 'all';

    public $streaming = false;

    public $scanned = [];

    public function scanQRCode(): void
    {
        ScannerFacade::scan()
            ->prompt(__('Scan the QR code, this can be found in you profile settings on the web app.'))
            ->id('auth-qr-scan');
    }

    #[OnNative(CodeScanned::class)]
    public function handleScan($data, $format, $id = null): void
    {
        Log::info('Scan QR code: ' . $data);
    }
    public function clearScans(): void
    {
        $this->scanned = [];
        $this->data = null;
        $this->format = null;
    }

    public function render()
    {
        return view('livewire.scanner')
            ->layout('components.layouts.app.home', [
                'title' => 'Scanner',
            ]);
    }
}
