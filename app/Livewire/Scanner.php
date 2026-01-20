<?php

namespace App\Livewire;

use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Scanner\CodeScanned;
use Native\Mobile\Facades\Scanner as ScannerFacade;

class Scanner extends Component
{
    public $data;
    public $format;
    public $requestedFormat = 'all';
    public $streaming = false;
    public $scanned = [];

    public function scan(): void
    {
        ScannerFacade::make()
            ->prompt($this->streaming ? 'Scan codes continuously' : 'Scan a code')
            ->formats([$this->requestedFormat])
            ->continuous($this->streaming);
    }

    #[OnNative(CodeScanned::class)]
    public function handleScanned($data, $format): void
    {
        if ($this->streaming) {
            $this->scanned[] = [
                'data' => $data,
                'format' => $format,
                'timestamp' => now()->format('H:i:s'),
            ];
        } else {
            $this->data = $data;
            $this->format = $format;

            // Auto-navigate if it's a URL
            if (filter_var($data, FILTER_VALIDATE_URL)) {
                $this->navigateToUrl($data);
            }
        }
    }

    public function navigateToUrl($url): void
    {
        // Extract the path from the URL
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';

        // Remove leading slash
        $path = ltrim($path, '/');

        // Match against old Wix routes first (ue1-themes-X/a-bientôt)
        if (preg_match('#^ue1-themes-(\d+)/a-bientôt$#', $path, $matches)) {
            // Just redirect to the old route and let Laravel handle the redirect
            $this->redirect('/' . $path);
            return;
        }

        // Match against your new route patterns
        if (preg_match('#^syllabus/([^/]+)/([^/]+)/([^/]+)$#', $path, $matches)) {
            // Route: /syllabus/{ue}/{theme}/{id}
            $this->redirect(route('syllabus.theme', [
                'ue' => $matches[1],
                'theme' => $matches[2],
                'id' => $matches[3]
            ]));
        } elseif (preg_match('#^syllabus/([^/]+)/([^/]+)$#', $path, $matches)) {
            // Route: /syllabus/{ue}/{theme}
            $this->redirect(route('syllabus.themes', [
                'ue' => $matches[1],
                'theme' => $matches[2]
            ]));
        } elseif (preg_match('#^syllabus/([^/]+)?$#', $path, $matches)) {
            // Route: /syllabus/{ue?}
            $ue = $matches[1] ?? null;
            $this->redirect(route('syllabus', ['ue' => $ue]));
        } elseif (preg_match('#^syllabus/?$#', $path)) {
            // Route: /syllabus
            $this->redirect(route('syllabus'));
        } else {
            // If no pattern matches, just redirect to the full URL path
            // This will catch any other old routes you might have
            $this->redirect('/' . $path);
        }
    }

    public function navigateToScanned($index): void
    {
        if (isset($this->scanned[$index])) {
            $scan = $this->scanned[$index];
            if (filter_var($scan['data'], FILTER_VALIDATE_URL)) {
                $this->navigateToUrl($scan['data']);
            }
        }
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