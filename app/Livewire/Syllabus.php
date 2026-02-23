<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Browser;
use Native\Mobile\Facades\Browser as BrowserFacade;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;
use Native\Mobile\Facades\System;

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
    public $userExcept;



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

        $this->userExcept = $user['id'];

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
                // Verificar si este tema está en los temas activos del usuario
                $theme['isActive'] = $activeThemes->contains($theme['attributes']['slug']);

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


// abrir modal de pago
    public function openPaymentModal($link): void
    {


        $this->selectedLink = $link;
     //   $this->showPaymentModal = true;

        Dialog::alert(
            'Accès Syllabus',
            'Ce contenu nécessite l\'achat du livre Syllabus. Voulez-vous ouvrir la boutique maintenant?',
            [
                'Oui, ouvrir la boutique',
                'Non, plus tard'
            ]
        )->id('payment-modal'); // ID único
    }


    #[OnNative(ButtonPressed::class)]
    public function handleAlert(int $index, string $id): void
    {
        // Manejar modal de pago
        if ($id === 'payment-modal' && $index === 0 && $this->selectedLink) {
            Browser::open($this->selectedLink);
        }


    }


    public function openInApp()
    {
        BrowserFacade::inApp('https://www.facebook.com/share/v/1BepzAgdKA');
    }

    public function openSystem()
    {
        BrowserFacade::open('https://nativephp.com');
    }



// cerrar modal
    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedSyllabuForPayment = null;
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
