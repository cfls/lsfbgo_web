<?php

namespace App\Livewire;

use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Scanner\CodeScanned;
use Native\Mobile\Facades\Scanner as ScannerFacade;
use Illuminate\Support\Facades\Log;

class Scanner extends Component
{
    public $data;
    public $format;
    public $requestedFormat = 'all';
    public $streaming = false;
    public $scanned = [];
    public $showWebView = false;
    public $webViewUrl = null;
    public $webViewTitle = null;
    public $canGoBack = false;
    public $canGoForward = false;
    public $isLoading = false;
    public $autoOpenUrls = true;
    public $scanHistory = [];
    public $showHistory = false;

    protected $listeners = ['refreshScanner' => '$refresh'];

    public function mount()
    {
        $this->scanHistory = session()->get('scan_history', []);

        // ← AGREGAR ESTO: Verificar si hay un deep link pendiente
        if (session()->has('deeplink_syllabus_url')) {
            $url = session()->get('deeplink_syllabus_url');
            session()->forget('deeplink_syllabus_url');

            // Abrir el syllabus automáticamente
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Ouverture depuis un lien direct...'
            ]);

            $this->openInApp($url);
        }
    }

    public function scan(): void
    {
        \Log::info('Scan method called');
        try {
            ScannerFacade::make()
                ->prompt($this->streaming ? 'Scanner en continu' : 'Scanner un code QR')
                ->formats([$this->requestedFormat])
                ->continuous($this->streaming);
        } catch (\Exception $e) {
            Log::error('Error al iniciar scanner: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'ouverture du scanner'
            ]);
        }
    }

    #[OnNative(CodeScanned::class)]
    public function handleScanned($data, $format): void
    {
        if ($this->data === $data && !$this->streaming) {
            return;
        }

        $scanData = [
            'data' => $data,
            'format' => $format,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'is_url' => $this->isUrl($data),
            'is_syllabus' => $this->isSyllabusUrl($data),
            'type' => $this->detectCodeType($data, $format),
        ];

        if ($this->streaming) {
            $this->scanned[] = $scanData;
        } else {
            $this->data = $data;
            $this->format = $format;

            // Auto-abrir URLs si está habilitado
            if ($this->autoOpenUrls && $this->isUrl($data)) {
                $this->openInApp($data);
            }
        }

        $this->addToHistory($scanData);
        $this->dispatch('vibrate', ['duration' => 100]);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Code scanné avec succès'
        ]);
    }

    private function isUrl($string): bool
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false
            || preg_match('/^(http|https):\/\//', $string)
            || preg_match('/^www\./', $string);
    }

    private function isSyllabusUrl($url): bool
    {
        return stripos($url, 'lsfbgo.cfls.be/syllabus') !== false;
    }

    private function detectCodeType($data, $format): string
    {
        if ($this->isSyllabusUrl($data)) {
            return 'Syllabus';
        }

        if ($this->isUrl($data)) {
            return 'URL';
        }

        if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
            return 'Email';
        }

        if (preg_match('/^tel:/', $data) || preg_match('/^\+?[0-9]{10,}$/', $data)) {
            return 'Téléphone';
        }

        if (preg_match('/^WIFI:/', $data)) {
            return 'WiFi';
        }

        if (preg_match('/^BEGIN:VCARD/', $data)) {
            return 'Contact';
        }

        if (preg_match('/^geo:/', $data)) {
            return 'Localisation';
        }

        if (is_numeric($data)) {
            return 'Numéro';
        }

        return 'Texte';
    }

    public function openInApp($url): void
    {
        // Asegurar que la URL tenga protocolo
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        $this->webViewUrl = $url;
        $this->showWebView = true;
        $this->isLoading = true;

        // Obtener título de la URL
        $this->webViewTitle = $this->getPageTitle($url);

        Log::info('Ouverture du WebView', ['url' => $url]);
    }

    private function getPageTitle($url): string
    {
        // Extraer título básico de la URL
        if ($this->isSyllabusUrl($url)) {
            $parts = explode('/', trim($url, '/'));
            $lastPart = end($parts);

            if ($lastPart === 'syllabus') {
                return 'Syllabus';
            }

            // Convertir "ue1-themes" a "UE1 Themes"
            $title = str_replace('-', ' ', $lastPart);
            $title = ucwords($title);

            return $title;
        }

        $host = parse_url($url, PHP_URL_HOST);
        return $host ?: 'Page Web';
    }

    public function closeWebView(): void
    {
        $this->showWebView = false;
        $this->webViewUrl = null;
        $this->webViewTitle = null;
        $this->isLoading = false;
        $this->canGoBack = false;
        $this->canGoForward = false;
    }

    public function reloadWebView(): void
    {
        $this->isLoading = true;
        $this->dispatch('reloadWebView');
    }

    public function goBack(): void
    {
        $this->dispatch('webViewGoBack');
    }

    public function goForward(): void
    {
        $this->dispatch('webViewGoForward');
    }

    public function openInBrowser(): void
    {
        if ($this->webViewUrl) {
            $this->dispatch('openExternal', ['url' => $this->webViewUrl]);
        }
    }

    public function webViewLoaded(): void
    {
        $this->isLoading = false;
    }

    public function updateNavigationState($canGoBack, $canGoForward): void
    {
        $this->canGoBack = $canGoBack;
        $this->canGoForward = $canGoForward;
    }

    public function openUrl($url): void
    {
        $this->openInApp($url);
    }

    public function copyToClipboard($text): void
    {
        $this->dispatch('copyToClipboard', ['text' => $text]);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Copié dans le presse-papiers'
        ]);
    }

    public function shareCode($data): void
    {
        $this->dispatch('shareContent', ['content' => $data]);
    }

    public function deleteFromHistory($index): void
    {
        if (isset($this->scanHistory[$index])) {
            unset($this->scanHistory[$index]);
            $this->scanHistory = array_values($this->scanHistory);
            $this->saveHistory();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Supprimé de l\'historique'
            ]);
        }
    }

    public function clearScans(): void
    {
        $this->scanned = [];
        $this->data = null;
        $this->format = null;
    }

    public function clearHistory(): void
    {
        $this->scanHistory = [];
        $this->saveHistory();
        $this->showHistory = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Historique effacé'
        ]);
    }

    public function toggleHistory(): void
    {
        $this->showHistory = !$this->showHistory;
    }

    private function addToHistory($scanData): void
    {
        if (!empty($this->scanHistory) && end($this->scanHistory)['data'] === $scanData['data']) {
            return;
        }

        array_unshift($this->scanHistory, $scanData);

        if (count($this->scanHistory) > 50) {
            array_pop($this->scanHistory);
        }

        $this->saveHistory();
    }

    private function saveHistory(): void
    {
        session()->put('scan_history', $this->scanHistory);
    }

    public function render()
    {
        return view('livewire.scanner')->layout('components.layouts.app.home', [
            'title' => 'Scanner QR',
        ]);
    }
}