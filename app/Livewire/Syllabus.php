<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Native\Mobile\Facades\Browser;
use Native\Mobile\Facades\SecureStorage;


class Syllabus extends Component
{
    public string $title = 'Mes Syllabus';
    public int $optionGame = 0;
    public $verifyUser;
    public $selectUE = [];   // aquí debe ser array, no string
    public $selectedUE = null;
    public $search = '';
    public $results = [];
    public $selectedTheme = false;
    public $themes = [];
    public $showPaymentModal = false;
    public $selectedSyllabuForPayment = null;
    public $selectedLink = null;
    public ?string $ue = null;
    public $role;
    public string $accessCode = '';
    public string $link = '';
    public string $theme = '';


    public function mount(?string $ue = null)
    {

        $this->ue = $ue;

        if ($this->ue) {
            // Lógica cuando hay un UE específico
            $this->loadTheme($this->ue);
        } else {
            // Lógica para listado general
            $this->loadAllThemes();
        }
    }

    public function loadAllThemes()
    {
        $api = app(ApiService::class);
        $dataUser = SecureStorage::get('data');
        $user = json_decode($dataUser, true)['user'];

        Log::info('User ID for Syllabus', ['data' => $user['role']]);
        
        $this->role = $user['role'] ?? 'unknown';
      
        // Obtener códigos verificados del usuario
        $verifyUser = $api->verifycodeStatus($user['id']);    
        $this->verifyUser = collect($verifyUser->json('data', []));

        // Obtener los NOMBRES de los temas activos (ue1-themes, ue2-themes)
        $activeThemes = $this->verifyUser
            ->where('attributes.active', 1)
            ->pluck('attributes.theme');

        // Obtener todos los temas disponibles
        $responseUser = $api->allThemes();
        $allThemes = collect($responseUser->json('data', []));

        // MOSTRAR TODOS los temas, pero marcar cuáles están activos
        $this->results = $allThemes
                ->map(function($theme) use ($activeThemes) {
                    $theme['isActive'] = $activeThemes->contains($theme['attributes']['slug']);

                    Log::info('Theme isActive', [
                        'slug'     => $theme['attributes']['slug'],
                        'isActive' => $theme['isActive'],
                    ]);

                    return $theme;
                })
                ->values()
                ->all();
    }

    public function loadTheme($ue)
    {


        $data = SecureStorage::get('data');
        $token = json_decode($data, true)['token'];
        $response = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken($token)
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/themes/' . $ue);
        // save in public property

        $this->selectedTheme = $response->json('data', []);
    }

  public function openPaymentModal($link, $theme): void
    {
        $this->theme = $theme; // resetear UE para evitar conflictos
        $this->selectedLink = $link;
        $this->showPaymentModal = true;
      
    }

    

    public function closePaymentModal(): void
    {
        $this->redirectRoute('syllabus');
    }

    public function openShop(): void
    {
        if ($this->selectedLink) {
            Browser::open($this->selectedLink);
        }
    }
      public function validateCode(): void
        {
            $this->validate([
                'accessCode' => ['required', 'string'],
            ]);

            $api = app(ApiService::class);
            $dataUser = SecureStorage::get('data');
            $user = json_decode($dataUser, true)['user'];

            $verifyUser = $api->Code($user['id'], $this->accessCode, $this->theme);      

           if ($verifyUser->successful() && $verifyUser->json('data.attributes.active') === 1) {
                $this->showPaymentModal = false;
                $this->accessCode = '';
            } else {
                $this->addError('accessCode', 'Code invalide');
            }
        }



    public function openInApp()
    {
        Browser::inApp('https://www.facebook.com/share/v/1BepzAgdKA');
    }

    public function openSystem()
    {
        Browser::open('https://nativephp.com');
    }




    public function render()
    {
        return view('livewire.syllabus', [
            'selectedTheme' => $this->selectedTheme,

        ])->layout('components.layouts.app.home',[
            'title' => 'Syllabus',
        ]);
    }
}
