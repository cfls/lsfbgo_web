<?php

namespace App\Livewire;

use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Scanner\CodeScanned;
use Native\Mobile\Facades\Scanner as ScannerFacade;

class Scanner extends Component  // Cambié el nombre de ScannerCopy a Scanner
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


        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';

        // Remove leading slash
        $path = ltrim($path, '/');

        // Decode URL (para manejar %C3%B4 -> ô)
        $path = urldecode($path);

        // Remove /a-bientôt from old Wix routes
        $path = preg_replace('#/a-bientôt$#', '', $path);

        // Caso 1b y 2b: Match ue{X}-themes/{theme} (con tema específico)
        if (preg_match('#^ue(\d+)-themes/(.+)$#', $path, $matches)) {
            $this->redirect('/' . $path);
            return;
        }

        // Caso 1b antiguo: Match ue1-themes-X (número al final)
        if (preg_match('#^ue(\d+)-themes-(\d+)$#', $path, $matches)) {
            $this->redirect('/' . $path);
            return;
        }

        // Caso 1a y 2a: Match ue{X}-themes (sin tema)
        if (preg_match('#^ue(\d+)-themes$#', $path, $matches)) {
            $this->redirect('/' . $path);
            return;
        }

        // Match against new syllabus route patterns
        if (preg_match('#^syllabus/([^/]+)/([^/]+)/([^/]+)$#', $path, $matches)) {
            $this->redirect(route('syllabus.theme', [
                'ue' => $matches[1],
                'theme' => $matches[2],
                'id' => $matches[3]
            ]));
        } elseif (preg_match('#^syllabus/([^/]+)/([^/]+)$#', $path, $matches)) {
            $this->redirect(route('syllabus.themes', [
                'ue' => $matches[1],
                'theme' => $matches[2]
            ]));
        } elseif (preg_match('#^syllabus/([^/]+)?$#', $path, $matches)) {
            $ue = $matches[1] ?? null;
            $this->redirect(route('syllabus', ['ue' => $ue]));
        } elseif (preg_match('#^syllabus/?$#', $path)) {
            $this->redirect(route('syllabus'));
        } else {
            // Fallback: redirect to the path as-is
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